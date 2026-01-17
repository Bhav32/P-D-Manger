import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { API_CONFIG } from '../config/api.config';

export interface Product {
  id: number;
  name: string;
  description: string;
  price: number;
  final_price?: number;
  savings?: number;
  discounts?: any[];
  created_at?: string;
  updated_at?: string;
}

export interface ProductListResponse {
  success: boolean;
  data: Product[];
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
export class ProductService {
  private apiUrl = `${API_CONFIG.baseUrl}${API_CONFIG.endpoints.products.list}`;

  constructor(private http: HttpClient) {}

  getProducts(params?: any): Observable<ProductListResponse> {
    let httpParams = new HttpParams();
    
    if (params) {
      if (params.search) httpParams = httpParams.set('search', params.search);
      if (params.sort_by) httpParams = httpParams.set('sort_by', params.sort_by);
      if (params.sort_order) httpParams = httpParams.set('sort_order', params.sort_order);
      if (params.per_page) httpParams = httpParams.set('per_page', params.per_page);
      if (params.page) httpParams = httpParams.set('page', params.page);
    }

    return this.http.get<ProductListResponse>(this.apiUrl, { params: httpParams });
  }

  getProduct(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/${id}`);
  }

  createProduct(product: any): Observable<any> {
    return this.http.post<any>(this.apiUrl, product);
  }

  updateProduct(id: number, product: any): Observable<any> {
    return this.http.put<any>(`${this.apiUrl}/${id}`, product);
  }

  deleteProduct(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }

  getProductDiscounts(productId: number): Observable<any> {
    return this.http.get(`${API_CONFIG.baseUrl}/api/products/${productId}/discounts`);
  }
}
