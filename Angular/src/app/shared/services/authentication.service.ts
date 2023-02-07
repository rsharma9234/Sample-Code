import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment';

@Injectable({
    providedIn: 'root'
})
export class AuthenticationService {

    headers;
    constructor(private http: HttpClient) { }

    login(loginData) {
        return new Promise((resolve, reject) => {
            this.http.post(environment.apiUrl + `/user/login`, loginData)
                .subscribe(
                    (data) => {
                        if (data['access_token']) {
                            localStorage.setItem('currentUser', JSON.stringify(data))
                            resolve(data);
                        }
                    },
                    (err) => {
                        reject(err)
                    });
        })

    }


    register(data) {
        return new Promise((resolve, reject) => {
            this.http.post(environment.apiUrl + `/user/register`, data)
                .subscribe(
                    (data) => {
                        resolve(data);
                    },
                    (err) => {
                        reject(err)
                    });
        })

    }




    logout() {
        // remove user from local storage to log user out
        localStorage.removeItem('currentUser');
    }
}
