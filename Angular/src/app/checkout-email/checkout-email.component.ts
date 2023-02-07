import { Component, OnInit } from '@angular/core';
import { GuestService } from '../shared/services/guest.service';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';

@Component({
  selector: 'app-checkout-email',
  templateUrl: './checkout-email.component.html',
  styleUrls: ['./checkout-email.component.scss']
})
export class CheckoutEmailComponent implements OnInit {

  checkoutEmailForm: FormGroup;
  valid: boolean;
  errorMessage: any = 'dfsdf';
  emailExistMessage: any;
  loading: boolean = false;


  validationMessages = {
      'email': {
          'required': 'Email is required.',
          'email': 'Email is invalid.'
      }
    }

  constructor(
    private guestService: GuestService,
    private fb: FormBuilder,
  ) { }

  ngOnInit() {
    this.createForm();
  }

  createForm() {
      this.checkoutEmailForm = this.fb.group({
          email: ['', [Validators.required, Validators.email]]
      });
  }

  onSentEmail() {
      console.log(this.checkoutEmailForm)
      if (this.checkoutEmailForm.invalid) {
          return;
      }
      this.loading = true;
      this.guestService.checkoutEmail(this.checkoutEmailForm.value).then(data => {
          alert("Email sent successfully.")
          this.loading = false
      }).catch((error) => {
          this.loading = false
          alert(error.error.message)
      })
  }

}
