import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service';

interface User {
  id: number;
  name: string;
  email: string;
}

interface Product {
  id: number;
  name: string;
  price: number;
  description: string;
  stock: number;
}

interface Discount {
  id: number;
  code: string;
  percentage: number;
  description: string;
}

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent implements OnInit {
  user: User | null = null;
  products: Product[] = [];
  discounts: Discount[] = [];
  loading = true;
  error = '';

  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.loadUserData();
  }

  loadUserData(): void {
    this.authService.getMe().subscribe({
      next: (response: any) => {
        this.user = response.data || response;
      },
      error: (error: any) => {
        console.error('Error loading user data', error);
        this.error = 'Failed to load user data';
      }
    });
  }

  logout(): void {
    this.authService.logout().subscribe({
      next: () => {
        this.router.navigate(['/login']);
      },
      error: (error: any) => {
        console.error('Error logging out', error);
        localStorage?.removeItem('token');
        this.router.navigate(['/login']);
      }
    });
  }

  goToProducts(): void {
    this.router.navigate(['/products']);
  }

  goToDiscounts(): void {
    this.router.navigate(['/discounts']);
  }

  getProfileInitial(user: User): string {
    // Generate an avatar with initials using an avatar service
    const initials = user.name
      .split(' ')
      .map(n => n.charAt(0).toUpperCase())
      .join('');
    
    // Generate a consistent color based on user ID
    const colors = ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c'];
    const colorIndex = user.id % colors.length;
    const backgroundColor = colors[colorIndex];
    
    // Return SVG as data URL for profile avatar
    const svg = `
      <svg width="120" height="120" xmlns="http://www.w3.org/2000/svg">
        <rect width="120" height="120" fill="${backgroundColor}"/>
        <text x="50%" y="50%" font-size="48" font-weight="bold" fill="white" 
              text-anchor="middle" dy=".3em" font-family="Arial, sans-serif">
          ${initials}
        </text>
      </svg>
    `;
    
    return `data:image/svg+xml;base64,${btoa(svg)}`;
  }
}
