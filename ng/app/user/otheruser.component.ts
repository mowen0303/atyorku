import {Component} from "@angular/core";
import {UserService} from "./user.service";
import {ActivatedRoute} from "@angular/router";
@Component({
    templateUrl:"./ng/app/user/otheruser.component.html",
    styleUrls:["./ng/app/user/otheruser.component.css"],
    selector:"other-user"
})

export class OtherUserComponent{
    info:Object;
    user_id:string;
    constructor(private userService:UserService,private route: ActivatedRoute){}

    ngOnInit(){
        this.route.params.subscribe(
            res=>{
                this.user_id=res["id"];
                this.getUserInfo(this.user_id);
            }
        )
    }

    getUserInfo(id:string){
        this.userService.getRowOfUserBasicInfoWithJson(id).subscribe(
            res=>{
                if(res["code"]==1){
                    this.info = res["result"];
                    this.info["gender"] = "resource/img/course_icon/"+this.info["gender"]+"g.png";
                }
            },
            error=>{
        }
        )
    }



}



