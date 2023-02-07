import { Component, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthenticationService } from '../shared/services/authentication.service';

@Component({
    selector: 'app-register',
    templateUrl: './register.component.html',
    styleUrls: ['./register.component.scss']
})
export class RegisterComponent implements OnInit {

    signupForm: FormGroup;
    valid: boolean;
    errorMessage: any = 'dfsdf';
    emailExistMessage: any;
    loading: boolean = false;


    validationMessages = {
        'firstname': {
            'required': 'First Name is Required.',
        },
        'lastname': {
            'required': 'Last Name is Required.',
        },
        'email': {
            'required': 'Email is required.',
            'email': 'Email is invalid.',
            'exist': 'Email is already exists.'
        },
        'password': {
            'required': 'Password is required.',
        },
        'confirmpass': {
            'required': 'Confirm Password is required.',
            'notSame': 'Confirm password does not match with password.'
        }
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
        this.signupForm = this.fb.group({
            first_name: ['', Validators.required],
            last_name: ['', Validators.required],
            email: ['', [Validators.required, Validators.email]],
            password: ['', [
                Validators.required,
            ]],
            confirmPass: ['', Validators.required],
        }, { validator: this.checkIfMatchingPasswords });
    }

    checkIfMatchingPasswords(group: FormGroup) { // here we have the 'passwords' group
        let pass = group.controls.password.value;
        let confirmPass = group.controls.confirmPass.value;
        return pass === confirmPass ? null : { notSame: true }
    }


    onRegister() {
        console.log(this.signupForm)
        if (this.signupForm.invalid) {
            return;
        }
        this.loading = true;
        delete this.signupForm.value.confirmPass;
        this.authService.register(this.signupForm.value).then(data => {
            alert("Registered Successfully.")
            this.loading = false
            this.router.navigate(['/login'])
        }).catch((error) => {
            this.loading = false
            alert(error.error.message)
        })
    }

}
