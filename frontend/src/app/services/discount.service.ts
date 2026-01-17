import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { API_CONFIG } from '../config/api.config';

export interface Discount {
  id: number;
  title: string;
  type: 'percentage' | 'fixed';
  value: number;
  is_active: boolean;
  products?: any[];
  created_at?: string;
  updated_at?: string;
}

export interface DiscountListResponse {
  success: boolean;
  data: Discount[];
  pagination: {
    total: number;
    per_page: number;
    current_page: number;
    last_page: number;
    from: number;
    to: number;
  };
}

@Injectable({
  providedIn: 'root'
})
export class DiscountService {
  private apiUrl = `${API_CONFIG.baseUrl}${API_CONFIG.endpoints.discounts.list}`;

  constructor(private http: HttpClient) {}

  getDiscounts(params?: any): Observable<DiscountListResponse> {
    let httpParams = new HttpParams();
    
    if (params) {
      if (params.search) httpParams = httpParams.set('search', params.search);
      if (params.type) httpParams = httpParams.set('type', params.type);
      if (params.sort_by) httpParams = httpParams.set('sort_by', params.sort_by);
      if (params.sort_order) httpParams = httpParams.set('sort_order', params.sort_order);
      if (params.per_page) httpParams = httpParams.set('per_page', params.per_page);
      if (params.page) httpParams = httpParams.set('page', params.page);
    }

    return this.http.get<DiscountListResponse>(this.apiUrl, { params: httpParams });
  }

  getDiscount(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/${id}`);
  }

  createDiscount(discount: any): Observable<any> {
    return this.http.post<any>(this.apiUrl, discount);
  }

  updateDiscount(id: number, discount: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/${id}`, discount);
  }

  deleteDiscount(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }

  getActiveDiscounts(): Observable<any> {
    return this.http.get(`${this.apiUrl}/active`);
  }
}
