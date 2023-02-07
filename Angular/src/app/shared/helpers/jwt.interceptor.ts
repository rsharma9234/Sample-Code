import { Injectable } from '@angular/core';
import { HttpRequest, HttpHandler, HttpEvent, HttpInterceptor } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable()
export class JwtInterceptor implements HttpInterceptor {
    intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        // add authorization header with jwt token if available
        let currentUser = JSON.parse(localStorage.getItem('currentUser'));
        let authRequest = request;
        if (currentUser && currentUser.access_token) {
            authRequest = request.clone({
                setHeaders: {
                    Authorization: 'Bearer ' + currentUser.access_token
                }
            });
        }
        // Handling Expired JWT Token Exception
        return next.handle(authRequest)
    }
}