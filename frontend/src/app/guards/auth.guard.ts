import { Injectable } from '@angular/core';
import { Router, CanActivateFn } from '@angular/router';
import { AuthService } from '../services/auth.service';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard {
  constructor(private authService: AuthService, private router: Router) {}
}

export const authGuard: CanActivateFn = (route, state) => {
  const authService = new AuthService(null as any);
  
  if (authService.isAuthenticated()) {
    return true;
  }
  
  // Redirect to login page
  window.location.href = '/login';
  return false;
};
