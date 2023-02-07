import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class GuestService {

  headers;
  constructor(private http: HttpClient) { }

  checkoutEmail(mailData) {
    return new Promise((resolve, reject) => {
      this.http.post(environment.apiUrl + `/guests/send-mail`, mailData)
        .subscribe(
          (data) => {
            resolve(data);
          },
          (err) => {
            reject(err)
          });
    })
  }

  checkout(gid, hotel) {
    return new Promise((resolve, reject) => {
      this.http.post(environment.apiUrl + `/guests/checkout`, { guestUUID: gid, subDomain: hotel})
        .subscribe(
          (data) => {
            resolve(data);
          },
          (err) => {
            reject(err)
          });
    })
  }

  getGuestList(hotelId, filter) {
    return new Promise((resolve, reject) => {
      this.http.post(environment.apiUrl + `/guests/list`, {hotel_id: hotelId, filter: filter})
        .subscribe(
          (data) => {
            resolve(data);
          },
          (err) => {
            reject(err)
          });
    })
  }

  getStatistics(hotelId, filterOption, beginDate?, endDate?) {
    return new Promise((resolve, reject) => {
      this.http.post(environment.apiUrl + `/guests/statistics`, {hotel_id: hotelId, filterOption: filterOption, fromDate: beginDate, toDate: endDate })
        .subscribe(
          (data) => {
            resolve(data);
          },
          (err) => {
            reject(err)
          });
    })
  }

  getStatisticsTable(hotelId) {
    return new Promise((resolve, reject) => {
      this.http.post(environment.apiUrl + `/guests/statistics-table`, {hotel_id: hotelId})
        .subscribe(
          (data) => {
            resolve(data);
          },
          (err) => {
            reject(err)
          });
    })
  }

}
