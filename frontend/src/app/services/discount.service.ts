import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { API_CONFIG } from '../config/api.config';

export interface Discount {
  id: number;
  code: string;
  percentage: number;
  description: string;
}

@Injectable({
  providedIn: 'root'
})
export class DiscountService {
  private apiUrl = `${API_CONFIG.baseUrl}${API_CONFIG.endpoints.discounts}`;

  constructor(private http: HttpClient) {}

  getDiscounts(): Observable<any> {
    return this.http.get<any>(this.apiUrl);
  }

  getDiscount(id: number): Observable<Discount> {
    return this.http.get<Discount>(`${this.apiUrl}/${id}`);
  }

  createDiscount(discount: any): Observable<Discount> {
    return this.http.post<Discount>(this.apiUrl, discount);
  }

  updateDiscount(id: number, discount: any): Observable<Discount> {
    return this.http.put<Discount>(`${this.apiUrl}/${id}`, discount);
  }

  deleteDiscount(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }
}
