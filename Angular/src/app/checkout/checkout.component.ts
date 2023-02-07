import { Component, OnInit, Inject } from '@angular/core';
import { ActivatedRoute, Params, Router } from '@angular/router';
import { GuestService } from '../shared/services/guest.service';
import {DOCUMENT} from '@angular/platform-browser';

@Component({
  selector: 'app-checkout',
  templateUrl: './checkout.component.html',
  styleUrls: ['./checkout.component.scss']
})
export class CheckoutComponent implements OnInit {

  guestId;
  message:any;
  host;

  constructor(
    @Inject(DOCUMENT) private document,
    private router: Router,
    private activatedRoute: ActivatedRoute,
    private guestService: GuestService
  ) {
    this.host = this.document.location.host;
    this.activatedRoute.queryParams.subscribe((params: Params) => {
      if (params.id) {
        this.guestId = params.id
        this.guestCheckout()
      } else {
        this.router.navigate(['/login'])
      }
    });
  }

  ngOnInit() {
  
  }

  guestCheckout() {
    this.guestService.checkout(this.guestId, this.getSubdomain(this.host)).then(data => {
      this.message = "success";
    }).catch((error) => {
      this.message = error.error.message
    })
  }

  getSubdomain(hostname) {
      var regexParse = new RegExp('[a-z\-0-9]{2,63}\.[a-z\.]{2,5}$');
      var urlParts = regexParse.exec(hostname);
      return urlParts != null ? hostname.replace(urlParts[0], '').slice(0, -1) : '';
  }

}
