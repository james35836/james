import { Injectable } from '@angular/core';
import { Observable, of } from 'rxjs';
import { map, catchError, tap } from 'rxjs/operators';
import { Http, Headers, Response } from '@angular/http';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import { User } from './user';
import { isDevMode } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable()
export class UserService 
{
    public oauthUrl = "/oauth/token";
    public usersUrl = "/api/user_data";
    public secretUrl = "/api/client_secret";
    public domain = "";

    constructor(private http: HttpClient) 
    {
        if(isDevMode()) 
        {
            this.domain = "http://james.test";
        }
        else
        {
            this.domain = "http://james.test";
        }
    }

    getClientSecret()
    {
        var headers = new HttpHeaders({
            "Content-Type": "application/json",
            "Accept": "application/json"
        });

        return this.http.get(this.domain + this.secretUrl, 
        {
            headers: headers
        });
    }

    getAccessToken(email: any, password: any, secret: any) 
    {
        var headers = new HttpHeaders({
            "Content-Type": "application/json",
            "Accept": "application/json"
        });

        var postData = 
        {
            grant_type: "password",
            client_id: 2,
            client_secret: secret,
            username: email,
            password: password,
            scope: "",
        }

        return this.http.post(this.domain + this.oauthUrl, JSON.stringify(postData), 
        {
            headers: headers
        });
    }

    getUserData(accessToken: string): Observable<User[]> 
    {
        var headers = new HttpHeaders({
            "Accept": "application/json",
            "Authorization": "Bearer " + accessToken,
        });
        
        return this.http.get<User[]>(this.domain + this.usersUrl, 
        {
            headers: headers
        });
    }

    findObjectByKey(array, key, value) : any
    {
        var ret = null;

        array.forEach(data =>
        {
            if(data[key] == value)
            {
                ret = data;
            }
        });

        return ret;
    }

    filterArrayByKey(array, key, value) : any
    {
        return array.filter(data => data[key] == value);
    }

    addCommas(nStr)
    {
        nStr += '';
        var x = nStr.split('.');
        var x1 = x[0];
        var x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }

    uploadImageOnServer(form_data, headers) : Observable<object>
    {
        var app                 = this;
        var sync_url            = app.domain + "/api/image/upload";

        return app.http.post(sync_url, form_data, {headers: headers});
    }

    getServiceCharge(service, headers)
    {
        return this.http.post(this.domain + "/api/service/charge", {service: service}, {headers: headers});
    }
    
    /**
     * Handle Http operation that failed.
     * Let the app continue.
     * @param operation - name of the operation that failed
     * @param result - optional value to return as the observable result
     */
    private handleError<T> (operation = 'operation', result?: T) {
      return (error: any): Observable<T> => {
     
        // TODO: send the error to remote logging infrastructure
        console.error(error); // log to console instead
     
        // TODO: better job of transforming error for user consumption
        // this.log(`${operation} failed: ${error.message}`);
     
        // Let the app keep running by returning an empty result.
        return of(result as T);
      };
    }
}