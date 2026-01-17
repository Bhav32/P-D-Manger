import { Routes } from '@angular/router';
import { LoginComponent } from './components/login/login.component';
import { DashboardComponent } from './components/dashboard/dashboard.component';
import { authGuard } from './guards/auth.guard'

export const routes: Routes = [
  { path: 'login', component: LoginComponent },
  { path: 'dashboard', component: DashboardComponent, canActivate: [authGuard] },
  {
    path: 'products',
    canActivate: [authGuard],
    children: [
      { path: '', loadComponent: () => import('./products/product-list.component').then(m => m.ProductListComponent) },
      { path: 'create', loadComponent: () => import('./products/product-form.component').then(m => m.ProductFormComponent) },
      { path: 'edit/:id', loadComponent: () => import('./products/product-form.component').then(m => m.ProductFormComponent) },
      { path: ':id', loadComponent: () => import('./products/product-list.component').then(m => m.ProductListComponent) } // Optional: single product view
    ]
  },
  {
    path: 'discounts',
    canActivate: [authGuard],
    children: [
      { path: '', loadComponent: () => import('./discounts/discount-list.component').then(m => m.DiscountListComponent) },
      { path: 'create', loadComponent: () => import('./discounts/discount-form.component').then(m => m.DiscountFormComponent) },
      { path: 'edit/:id', loadComponent: () => import('./discounts/discount-form.component').then(m => m.DiscountFormComponent) },
      { path: ':id', loadComponent: () => import('./discounts/discount-list.component').then(m => m.DiscountListComponent) } // Optional: single discount view
    ]
  },
  { path: '', redirectTo: '/dashboard', pathMatch: 'full' }
];
