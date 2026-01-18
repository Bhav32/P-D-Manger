import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, FormArray, Validators, ReactiveFormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ProductService, Product } from '../services/product.service';
import { DiscountService, Discount } from '../services/discount.service';

@Component({
  selector: 'app-product-form',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './product-form.component.html',
  styleUrl: './product-form.component.scss'
})
export class ProductFormComponent implements OnInit {
  private fb = inject(FormBuilder);
  private productService = inject(ProductService);
  private discountService = inject(DiscountService);
  private route = inject(ActivatedRoute);
  private router = inject(Router);

  productForm!: FormGroup;
  isEditMode = false;
  isSubmitting = false;
  showSuccessMessage = false;
  successMessage = '';
  productId: number | null = null;
  availableDiscounts: Discount[] = [];
  discountMap: { [key: number]: Discount } = {};

  ngOnInit() {
    this.initializeForm();
    this.loadAvailableDiscounts();
    this.checkEditMode();
  }

  private loadAvailableDiscounts() {
    this.discountService.getDiscounts({ per_page: 100 }).subscribe({
      next: (response) => {
        this.availableDiscounts = response.data || [];
        // Create a map for easy lookup
        this.availableDiscounts.forEach(discount => {
          this.discountMap[discount.id] = discount;
        });
      },
      error: (error) => {
        console.error('Error loading discounts:', error);
      }
    });
  }

  private initializeForm() {
    this.productForm = this.fb.group({
      name: ['', [Validators.required, Validators.minLength(3), Validators.maxLength(255)]],
      description: ['', Validators.maxLength(1000)],
      price: ['', [Validators.required, Validators.min(0)]],
      final_price: [{ value: '', disabled: true }],
      discounts: this.fb.array([])
    });
  }

  private checkEditMode() {
    this.route.paramMap.subscribe(params => {
      const id = params.get('id');
      if (id) {
        this.isEditMode = true;
        this.productId = +id;
        this.loadProduct(this.productId);
      }
    });
  }

  private loadProduct(id: number) {
    this.productService.getProduct(id).subscribe({
      next: (response: any) => {
        const product = response.data || response;
        this.populateForm(product);
      },
      error: (error) => {
        console.error('Error loading product:', error);
        alert('Failed to load product');
        this.goBack();
      }
    });
  }

  private populateForm(product: Product) {
    this.productForm.patchValue({
      name: product.name,
      description: product.description,
      price: product.price,
      final_price: product.final_price || product.price
    });

    // Clear existing discounts and add the product's discounts
    while (this.discounts.length > 0) {
      this.discounts.removeAt(0);
    }

    if (product.discounts && product.discounts.length > 0) {
      product.discounts.forEach((discount: any) => {
        this.discounts.push(this.createDiscountFormGroup(discount));
      });
    }

    // Update final price on price change
    this.productForm.get('price')?.valueChanges.subscribe(() => {
      this.updateFinalPrice();
    });
  }

  get discounts(): FormArray {
    return this.productForm.get('discounts') as FormArray;
  }

  private createDiscountFormGroup(discount?: any): FormGroup {
    const group = this.fb.group({
      id: [discount?.id || '', Validators.required],
      type: [discount?.type || '', Validators.required],
      value: [discount?.value || '', [Validators.required, Validators.min(0)]]
    });

    // Listen to id changes and auto-fill type and value
    group.get('id')?.valueChanges.subscribe((discountId) => {
      if (discountId) {
        const selectedDiscount = this.discountMap[discountId];
        if (selectedDiscount) {
          group.patchValue({
            type: selectedDiscount.type,
            value: selectedDiscount.value
          });
        }
      }
    });

    return group;
  }

  addDiscount() {
    this.discounts.push(this.createDiscountFormGroup());
  }

  removeDiscount(index: number) {
    this.discounts.removeAt(index);
  }

  getDiscountName(discountId: number): string {
    const discount = this.discountMap[discountId];
    return discount ? discount.title : 'Unknown Discount';
  }

  private updateFinalPrice() {
    const basePrice = this.productForm.get('price')?.value || 0;
    let finalPrice = basePrice;

    const discountsArray = this.discounts.value;
    if (discountsArray && discountsArray.length > 0) {
      discountsArray.forEach((discount: any) => {
        if (discount.type === 'percentage') {
          finalPrice -= finalPrice * (discount.value / 100);
        } else if (discount.type === 'fixed') {
          finalPrice -= discount.value;
        }
      });
    }

    this.productForm.get('final_price')?.setValue(Math.max(0, finalPrice));
  }

  isFieldInvalid(fieldName: string): boolean {
    const field = this.productForm.get(fieldName);
    return !!(field && field.invalid && (field.dirty || field.touched));
  }

  getErrorMessage(fieldName: string): string {
    const control = this.productForm.get(fieldName);
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
      name: 'Product Name',
      price: 'Original Price'
    };
    return labels[fieldName] || fieldName;
  }

  onSubmit() {
    if (!this.productForm.valid) {
      return;
    }

    this.isSubmitting = true;
    
    // Extract only discount IDs for the API request
    const discountIds = this.discounts.value
      .map((d: any) => d.id)
      .filter((id: any) => id); // Filter out empty values

    const formData = {
      name: this.productForm.get('name')?.value,
      description: this.productForm.get('description')?.value,
      price: this.productForm.get('price')?.value,
      discounts: discountIds
    };

    const request = this.isEditMode && this.productId
      ? this.productService.updateProduct(this.productId, formData)
      : this.productService.createProduct(formData);

    request.subscribe({
      next: (response) => {
        this.isSubmitting = false;
        this.showSuccessMessage = true;
        this.successMessage = this.isEditMode ? 'Product updated successfully!' : 'Product created successfully!';
        
        setTimeout(() => {
          this.router.navigate(['/products']);
        }, 1500);
      },
      error: (error) => {
        this.isSubmitting = false;
        console.error('Error saving product:', error);
        alert(error.error?.message || 'Failed to save product');
      }
    });
  }

  goBack() {
    this.router.navigate(['/products']);
  }
}
