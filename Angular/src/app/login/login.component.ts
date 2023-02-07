import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { AuthenticationService } from '../shared/services/authentication.service';

@Component({
    selector: 'app-login',
    templateUrl: './login.component.html',
    styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {

    loginForm: FormGroup;
    valid: boolean;
    errorMessage: any;
    loading: boolean = false;
    

    validationMessages = {
        'email': {
            'required': 'Email is required.',
            'email': 'Email is invalid.',
        },
        'password': {
            'required': 'Password is required.',
            'notMatch': 'Password pattern is not valid.',
        },
    }


    constructor(
        private router: Router,
        private fb: FormBuilder,
        private authService: AuthenticationService,
    ) { }

    ngOnInit() {
        let currentUser = JSON.parse(localStorage.getItem('currentUser'));
        if (currentUser && currentUser.access_token) {            
            this.router.navigate(['/guests']);
        }
        this.createForm();
    }

    createForm() {
        this.loginForm = this.fb.group({
            email: ['', [Validators.required, Validators.email]],
            password: ['', [
                Validators.required,
            ]],
        });
    }

    onLogin() {
        if (this.loginForm.invalid) {
            return;
        }
        this.loading = true;
        this.authService.login(this.loginForm.value).then(data => {
            this.loading = false
            this.router.navigate(['/guests'])
        }).catch((error) => {
            this.loading = false
            alert(error.error.message)
        })
    }
}
