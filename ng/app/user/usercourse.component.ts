import {Component} from "@angular/core";
import {UserService} from "./user.service";
import { CookieService } from 'angular2-cookie/services/cookies.service';
import {ForumService} from "../forum/forum.service";
import {Router} from "@angular/router";
@Component({
    templateUrl:"./ng/app/user/usercourse.component.html",
    styleUrls:["./ng/app/user/usercourse.component.css"],
    selector:"user-course"
})

export class UserCourseComponent{

    courses:Object[]=[];
    constructor(private userService:UserService,private cookieService:CookieService,private forumService:ForumService,private router:Router){}

    ngOnInit(){
        this.getCourses(this.cookieService.get("cc_id"));
    }

    getCourses(id:string){
        this.userService.getCourseListSpecificToUserCommentWithJson(id).subscribe(
            res=>{
                if (res["code"]==1){
                    this.courses = res.result;
                    this.courses.forEach((v,i,a)=> {
                        a[i]["diff"] = "resource/img/course_icon/" + Math.floor(+a[i]["diff"]) + ".png";
                    });
                }
            }
            )
    }
    navi(id:String){
        this.router.navigate(["/course/detail",id]);
    }
}



