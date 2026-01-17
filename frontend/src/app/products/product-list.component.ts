import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatTableModule } from '@angular/material/table';
import { MatPaginatorModule, PageEvent } from '@angular/material/paginator';
import { MatSortModule, Sort } from '@angular/material/sort';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatInputModule } from '@angular/material/input';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { Router, RouterModule } from '@angular/router';
import { ProductService, Product } from '../services/product.service';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-product-list',
  standalone: true,
  imports: [
    CommonModule,
    MatTableModule,
    MatPaginatorModule,
    MatSortModule,
    MatButtonModule,
    MatIconModule,
    MatInputModule,
    MatProgressSpinnerModule,
    RouterModule,
    FormsModule
  ],
  templateUrl: './product-list.component.html',
  styleUrl: './product-list.component.scss'
})
export class ProductListComponent implements OnInit {
  public Math = Math;
  private productService = inject(ProductService);
  private router = inject(Router);

  displayedColumns: string[] = ['name', 'description', 'price', 'final_price', 'savings', 'discounts', 'actions'];
  products: Product[] = [];
  total = 0;
  pageSize = 5;
  pageIndex = 0;
  search = '';
  sortField = 'name';
  sortDirection: 'asc' | 'desc' = 'asc';
  loading = false;

  // Delete modal properties
  showDeleteModal = false;
  productToDelete: Product | null = null;
  isDeleting = false;

  // Success message
  showSuccessMessage = false;
  successMessage = '';

  sortOptions = [
    { value: 'name', label: 'Name' },
    { value: 'price', label: 'Original Price' },
    { value: 'final_price', label: 'Final Price' },
    { value: 'savings', label: 'Savings' }
  ];
  pageSizeOptions = [5, 10, 20, 50];

  ngOnInit() {
    this.fetchProducts();
  }

  fetchProducts() {
    this.loading = true;
    const params: any = {
      page: this.pageIndex + 1,
      per_page: this.pageSize,
      search: this.search,
      sort_by: this.sortField,
      sort_order: this.sortDirection
    };
    this.productService.getProducts(params).subscribe({
      next: (res) => {
        this.products = res.data;
        this.total = res.pagination.total;
        this.loading = false;
      },
      error: () => {
        this.loading = false;
      }
    });
  }

  onPage(event: PageEvent) {
    this.pageIndex = event.pageIndex;
    this.pageSize = event.pageSize;
    this.fetchProducts();
  }

  onSortFieldChange(field: string) {
    this.sortField = field;
    this.fetchProducts();
  }

  onSortDirectionChange(direction: 'asc' | 'desc') {
    this.sortDirection = direction;
    this.fetchProducts();
  }

  onSearch() {
    this.pageIndex = 0;
    this.fetchProducts();
  }

  clearSearch() {
    this.search = '';
    this.onSearch();
  }

  goToCreate() {
    this.router.navigate(['/products/create']);
  }

  goToEdit(product: Product) {
    this.router.navigate(['/products/edit', product.id]);
  }

  goToDetails(product: Product) {
    this.router.navigate(['/products', product.id]);
  }

  deleteProduct(product: Product) {
    this.productToDelete = product;
    this.showDeleteModal = true;
  }

  cancelDelete() {
    this.showDeleteModal = false;
    this.productToDelete = null;
  }

  confirmDelete() {
    if (!this.productToDelete) return;

    this.isDeleting = true;
    this.productService.deleteProduct(this.productToDelete.id).subscribe({
      next: () => {
        this.isDeleting = false;
        this.showDeleteModal = false;
        this.showSuccessMessage = true;
        this.successMessage = 'Product deleted successfully!';
        
        setTimeout(() => {
          this.showSuccessMessage = false;
        }, 3000);

        // Refresh the products list
        this.fetchProducts();
      },
      error: (error) => {
        this.isDeleting = false;
        console.error('Error deleting product:', error);
        alert(error.error?.message || 'Failed to delete product');
      }
    });
  }

  getDiscountDisplay(product: Product): string[] {
    if (!product.discounts || product.discounts.length === 0) return [];
    return product.discounts.map((d: any) => `${d.title || d.code || d.type} (${d.value}${d.type === 'percentage' ? '%' : ''})`);
  }

  getFinalPrice(product: Product): number {
    if (product.final_price !== undefined && product.final_price !== null) {
      return product.final_price;
    }
    let price = product.price;
    if (product.discounts && product.discounts.length > 0) {
      product.discounts.forEach((d: any) => {
        if (d.type === 'percentage') {
          price -= price * (d.value / 100);
        } else if (d.type === 'fixed') {
          price -= d.value;
        }
      });
    }
    return price;
  }

  getSavings(product: Product): number | null {
    const final = this.getFinalPrice(product);
    if (final < product.price) {
      return product.price - final;
    }
    return null;
  }
}
