import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { DiscountService, Discount } from '../services/discount.service';

@Component({
  selector: 'app-discount-view',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './discount-view.component.html',
  styleUrl: './discount-view.component.scss'
})
export class DiscountViewComponent implements OnInit {
  private discountService = inject(DiscountService);
  private route = inject(ActivatedRoute);
  private router = inject(Router);

  discount: Discount | null = null;
  loading = false;
  error = '';

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.loadDiscount(parseInt(id));
    }
  }

  loadDiscount(id: number) {
    this.loading = true;
    this.error = '';
    
    this.discountService.getDiscount(id).subscribe({
      next: (response: any) => {
        this.discount = response.data || response;
        this.loading = false;
      },
      error: (error: any) => {
        console.error('Error loading discount:', error);
        this.error = 'Failed to load discount details';
        this.loading = false;
      }
    });
  }

  goBack() {
    this.router.navigate(['/discounts']);
  }

  goToEdit(id: number) {
    this.router.navigate(['/discounts/edit', id]);
  }

  formatDiscountValue(value: any): string {
    const numValue = Number(value);
    return numValue.toFixed(2);
  }
}
