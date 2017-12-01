import { Injectable }       from '@angular/core';
import {
    CanActivate, Router,
    ActivatedRouteSnapshot,
    RouterStateSnapshot
}                           from '@angular/router';
import {LoginService} from "./login.service";

@Injectable()
export class AuthGuard implements CanActivate {
    constructor(private loginService: LoginService, private router: Router) {}

    canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): boolean {
        let url = state.url;
        if (this.loginService.logged_in){
            return true;
        }
        else {
            this.loginService.login_hide_change.next({login_hide: false, url: url});
            return false;
        }
    }


}