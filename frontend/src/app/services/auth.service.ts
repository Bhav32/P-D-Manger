import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, BehaviorSubject } from 'rxjs';
import { tap } from 'rxjs/operators';
import { API_CONFIG } from '../config/api.config';

export interface AuthResponse {
  access_token: string;
  token_type: string;
  user: {
    id: number;
    name: string;
    email: string;
  };
}

export interface LoginCredentials {
  email: string;
  password: string;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = `${API_CONFIG.baseUrl}${API_CONFIG.endpoints.auth.login}`;
  private tokenSubject = new BehaviorSubject<string | null>(this.getToken());
  public token$ = this.tokenSubject.asObservable();

  constructor(private http: HttpClient) {}

  login(credentials: LoginCredentials): Observable<AuthResponse> {
    const loginUrl = `${API_CONFIG.baseUrl}${API_CONFIG.endpoints.auth.login}`;
    return this.http.post<AuthResponse>(loginUrl, credentials).pipe(
      tap(response => {
        if (typeof localStorage !== 'undefined') {
          localStorage.setItem('token', response.access_token);
        }
        this.tokenSubject.next(response.access_token);
      })
    );
  }

  logout(): Observable<any> {
    const logoutUrl = `${API_CONFIG.baseUrl}${API_CONFIG.endpoints.auth.logout}`;
    return this.http.post(logoutUrl, {}).pipe(
      tap(() => {
        if (typeof localStorage !== 'undefined') {
          localStorage?.removeItem('token');
        }
        this.tokenSubject.next(null);
      })
    );
  }

  getMe(): Observable<any> {
    return this.http.get(`${API_CONFIG.baseUrl}/auth/user`);
  }

  refresh(): Observable<AuthResponse> {
    return this.http.post<AuthResponse>(`${API_CONFIG.baseUrl}/auth/refresh`, {}).pipe(
      tap(response => {
        if (typeof localStorage !== 'undefined') {
          localStorage?.setItem('token', response.access_token);
        }
        this.tokenSubject.next(response.access_token);
      })
    );
  }

  getToken(): string | null {
    if (typeof localStorage !== 'undefined') {
      const token = localStorage.getItem('token');
      return token;
    }
    return null;
  }

  isAuthenticated(): boolean {
    return !!this.getToken();
  }
}
