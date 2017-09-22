import {Component} from "@angular/core";
import {UserService} from "./user.service";
import { CookieService } from 'angular2-cookie/services/cookies.service';
@Component({
    templateUrl:"./ng/app/user/user.component.html",
    styleUrls:["./ng/app/user/user.component.css"],
    selector:"user"
})

export class UserComponent{

    info:Object;

    constructor(private userService:UserService,private cookieService:CookieService){}

    ngOnInit(){
        this.userService.getRowOfUserWithJson(this.cookieService.get("cc_id")).subscribe(
            res=>{
                this.info = res["result"];
                this.userService.user_info=this.info;
                this.userService.user_info_observable.next(this.info);
                console.log(res);
                this.info["gender"]= "resource/img/course_icon/"+this.info["gender"]+"g.png";
            }
        )
    }
}


