import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { DiscountService, Discount } from '../services/discount.service';

@Component({
  selector: 'app-discount-list',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './discount-list.component.html',
  styleUrl: './discount-list.component.scss'
})
export class DiscountListComponent implements OnInit {
  private discountService = inject(DiscountService);
  private router = inject(Router);

  discounts: Discount[] = [];
  filteredDiscounts: Discount[] = [];
  loading = false;

  // Search and filter properties
  search = '';
  filterType: string = 'all';
  sortBy: string = 'created_at';
  sortOrder: 'asc' | 'desc' = 'desc';

  // Pagination
  pageSize = 12;
  pageIndex = 0;
  total = 0;

  // Modal properties
  showDeleteModal = false;
  discountToDelete: Discount | null = null;
  isDeleting = false;
  showSuccessMessage = false;
  successMessage = '';

  // Filter and sort options
  typeOptions = [
    { value: 'all', label: 'All Types' },
    { value: 'percentage', label: 'Percentage' },
    { value: 'fixed', label: 'Fixed Amount' }
  ];

  sortOptions = [
    { value: 'created_at', label: 'Date Created' },
    { value: 'title', label: 'Name' },
    { value: 'value', label: 'Value' }
  ];

  ngOnInit() {
    this.fetchDiscounts();
  }

  fetchDiscounts() {
    this.loading = true;
    const params: any = {
      page: this.pageIndex + 1,
      per_page: this.pageSize,
      search: this.search,
      sort_by: this.sortBy,
      sort_order: this.sortOrder
    };

    if (this.filterType !== 'all') {
      params.type = this.filterType;
    }

    this.discountService.getDiscounts(params).subscribe({
      next: (res) => {
        console.log('Discounts received:', res);
        this.discounts = res.data || [];
        this.total = res.pagination?.total || 0;
        this.applyLocalFiltering();
        this.loading = false;
      },
      error: (error) => {
        console.error('Error fetching discounts:', error);
        this.loading = false;
      }
    });
  }

  applyLocalFiltering() {
    this.filteredDiscounts = [...this.discounts];
    
    // Apply search filter
    if (this.search.trim()) {
      const searchLower = this.search.toLowerCase();
      this.filteredDiscounts = this.filteredDiscounts.filter(d =>
        d.title.toLowerCase().includes(searchLower)
      );
    }

    // Apply type filter
    if (this.filterType !== 'all') {
      this.filteredDiscounts = this.filteredDiscounts.filter(d =>
        d.type === this.filterType
      );
    }
  }

  onSearch() {
    this.pageIndex = 0;
    this.fetchDiscounts();
  }

  onFilterTypeChange() {
    this.pageIndex = 0;
    this.fetchDiscounts();
  }

  onSortChange() {
    this.pageIndex = 0;
    this.fetchDiscounts();
  }

  onToggleSortOrder() {
    this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
    this.pageIndex = 0;
    this.fetchDiscounts();
  }

  goToCreate() {
    this.router.navigate(['/discounts/create']);
  }

  goToEdit(discount: Discount) {
    this.router.navigate(['/discounts/edit', discount.id]);
  }

  goToView(discount: Discount) {
    this.router.navigate(['/discounts', discount.id]);
  }

  deleteDiscount(discount: Discount) {
    this.discountToDelete = discount;
    this.showDeleteModal = true;
  }

  cancelDelete() {
    this.showDeleteModal = false;
    this.discountToDelete = null;
  }

  confirmDelete() {
    if (!this.discountToDelete) return;

    this.isDeleting = true;
    this.discountService.deleteDiscount(this.discountToDelete.id).subscribe({
      next: () => {
        this.isDeleting = false;
        this.showDeleteModal = false;
        this.showSuccessMessage = true;
        this.successMessage = 'Discount deleted successfully!';

        setTimeout(() => {
          this.showSuccessMessage = false;
        }, 3000);

        this.fetchDiscounts();
      },
      error: (error) => {
        this.isDeleting = false;
        console.error('Error deleting discount:', error);
        alert(error.error?.message || 'Failed to delete discount');
      }
    });
  }

  getDiscountDescription(discount: Discount): string {
    if (discount.type === 'percentage') {
      return `Get ${Number(discount.value).toFixed(2)}% off`;
    } else {
      return `Save ₹${Number(discount.value).toFixed(2)}`;
    }
  }

  formatDiscountValue(discount: Discount): string {
    const value = Number(discount.value).toFixed(2);
    if (discount.type === 'percentage') {
      return value + '%';
    } else {
      return '₹' + value;
    }
  }

  getProductCount(discount: Discount): number {
    return discount.products?.length || 0;
  }

  getStatusClass(discount: Discount): string {
    return discount.is_active ? 'status-active' : 'status-inactive';
  }

  getStatusText(discount: Discount): string {
    return discount.is_active ? 'ACTIVE' : 'INACTIVE';
  }

  formatDate(date: string | undefined): string {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  }

  get paginatedDiscounts(): Discount[] {
    const start = this.pageIndex * this.pageSize;
    const end = start + this.pageSize;
    return this.filteredDiscounts.slice(start, end);
  }

  get totalPages(): number {
    return Math.ceil(this.filteredDiscounts.length / this.pageSize);
  }

  previousPage() {
    if (this.pageIndex > 0) {
      this.pageIndex--;
    }
  }

  nextPage() {
    if (this.pageIndex < this.totalPages - 1) {
      this.pageIndex++;
    }
  }

  goToPage(page: number) {
    this.pageIndex = page;
  }
}
