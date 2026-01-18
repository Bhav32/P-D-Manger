import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { ProductService, Product } from '../services/product.service';

@Component({
  selector: 'app-product-view',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './product-view.component.html',
  styleUrl: './product-view.component.scss'
})
export class ProductViewComponent implements OnInit {
  private productService = inject(ProductService);
  private route = inject(ActivatedRoute);
  private router = inject(Router);

  product: Product | null = null;
  loading = false;
  error = '';

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.loadProduct(parseInt(id));
    }
  }

  loadProduct(id: number) {
    this.loading = true;
    this.error = '';
    
    // Get product details with discounts
    this.productService.getProduct(id).subscribe({
      next: (response: any) => {
        this.product = response.data || response;
        this.loading = false;
      },
      error: (error: any) => {
        console.error('Error loading product:', error);
        this.error = 'Failed to load product details';
        this.loading = false;
      }
    });
  }

  goBack() {
    this.router.navigate(['/products']);
  }

  goToEdit(id: number) {
    this.router.navigate(['/products/edit', id]);
  }

  getFinalPrice(): number {
    if (!this.product) return 0;
    
    if (this.product.final_price !== undefined && this.product.final_price !== null) {
      return this.product.final_price;
    }

    let price = this.product.price;
    if (this.product.discounts && this.product.discounts.length > 0) {
      this.product.discounts.forEach((d: any) => {
        if (d.type === 'percentage') {
          price -= price * (d.value / 100);
        } else if (d.type === 'fixed') {
          price -= d.value;
        }
      });
    }
    return price;
  }

  getSavings(): number | null {
    if (!this.product) return null;
    const final = this.getFinalPrice();
    if (final < this.product.price) {
      return this.product.price - final;
    }
    return null;
  }

  getSavingsPercentage(): number | null {
    if (!this.product) return null;
    const savings = this.getSavings();
    if (savings) {
      return (savings / this.product.price) * 100;
    }
    return null;
  }
}
