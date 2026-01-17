import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { DiscountService, Discount } from '../services/discount.service';

@Component({
  selector: 'app-discount-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './discount-form.component.html',
  styleUrl: './discount-form.component.scss'
})
export class DiscountFormComponent implements OnInit {
  private fb = inject(FormBuilder);
  private discountService = inject(DiscountService);
  private route = inject(ActivatedRoute);
  private router = inject(Router);

  discountForm!: FormGroup;
  isEditMode = false;
  isSubmitting = false;
  showSuccessMessage = false;
  successMessage = '';
  discountId: number | null = null;

  typeOptions = [
    { value: 'percentage', label: 'Percentage (%)' },
    { value: 'fixed', label: 'Fixed Amount (₹)' }
  ];

  ngOnInit() {
    this.initializeForm();
    this.checkEditMode();
  }

  private initializeForm() {
    this.discountForm = this.fb.group({
      title: ['', [Validators.required, Validators.minLength(3), Validators.maxLength(255)]],
      type: ['percentage', Validators.required],
      value: ['', [Validators.required, Validators.min(0)]],
      is_active: [true]
    });
  }

  private checkEditMode() {
    this.route.paramMap.subscribe(params => {
      const id = params.get('id');
      if (id) {
        this.isEditMode = true;
        this.discountId = +id;
        this.loadDiscount(this.discountId);
      }
    });
  }

  private loadDiscount(id: number) {
    this.discountService.getDiscount(id).subscribe({
      next: (response: any) => {
        const discount = response.data || response;
        this.populateForm(discount);
      },
      error: (error) => {
        console.error('Error loading discount:', error);
        alert('Failed to load discount');
        this.goBack();
      }
    });
  }

  private populateForm(discount: Discount) {
    this.discountForm.patchValue({
      title: discount.title,
      type: discount.type,
      value: discount.value,
      is_active: discount.is_active
    });
  }

  isFieldInvalid(fieldName: string): boolean {
    const field = this.discountForm.get(fieldName);
    return !!(field && field.invalid && (field.dirty || field.touched));
  }

  getErrorMessage(fieldName: string): string {
    const control = this.discountForm.get(fieldName);
    if (control?.hasError('required')) {
      return `${this.getFieldLabel(fieldName)} is required`;
    }
    if (control?.hasError('minlength')) {
      return `Minimum length is ${control.getError('minlength').requiredLength}`;
    }
    if (control?.hasError('maxlength')) {
      return `Maximum length is ${control.getError('maxlength').requiredLength}`;
    }
    if (control?.hasError('min')) {
      return `Value must be at least ${control.getError('min').min}`;
    }
    return 'Invalid input';
  }

  private getFieldLabel(fieldName: string): string {
    const labels: { [key: string]: string } = {
      title: 'Discount Name',
      type: 'Discount Type',
      value: 'Discount Value'
    };
    return labels[fieldName] || fieldName;
  }

  onSubmit() {
    if (!this.discountForm.valid) {
      return;
    }

    this.isSubmitting = true;
    const formData = this.discountForm.value;

    const request = this.isEditMode && this.discountId
      ? this.discountService.updateDiscount(this.discountId, formData)
      : this.discountService.createDiscount(formData);

    request.subscribe({
      next: (response) => {
        this.isSubmitting = false;
        this.showSuccessMessage = true;
        this.successMessage = this.isEditMode ? 'Discount updated successfully!' : 'Discount created successfully!';

        setTimeout(() => {
          this.router.navigate(['/discounts']);
        }, 1500);
      },
      error: (error) => {
        this.isSubmitting = false;
        console.error('Error saving discount:', error);
        alert(error.error?.message || 'Failed to save discount');
      }
    });
  }

  goBack() {
    this.router.navigate(['/discounts']);
  }
}
