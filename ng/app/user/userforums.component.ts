import {Component} from "@angular/core";
import {UserService} from "./user.service";
import { CookieService } from 'angular2-cookie/services/cookies.service';
import {ForumService} from "../forum/forum.service";
import {ActivatedRoute} from "@angular/router";
import {DomSanitizer} from "@angular/platform-browser";
@Component({
    templateUrl:"./ng/app/user/userforums.component.html",
    styleUrls:["./ng/app/user/userforums.component.css"],
    selector:"user-forum"
})

export class UserForumComponent{

    pyqs:Object[];
    currentPage:number;
    page_loading:boolean
    constructor(private userService:UserService,private cookieService:CookieService,private forumService:ForumService,private route: ActivatedRoute,private sanitizer:DomSanitizer){}

    ngOnInit(){
        this.route.params.subscribe(
            res=>{
                this.pyqs = [];
                this.page_loading=false;
                this.currentPage=1;
                if(res["action"]=="postedForums"){
                    this.getForums(this.cookieService.get("cc_id"),this.currentPage);
                }
                else if (res["action"]=="commentedForums"){
                    this.getCommentedForums(this.cookieService.get("cc_id"),this.currentPage);
                }
                window.scroll(0,0);
            },
            error=>{
            }
        )
    }

    getForums(id:string,page:number){
        this.userService.getForumListOfSpecificUserWithJson(id,page).subscribe(
            res=>{
                if (res["code"]==1){
                    this.page_loading=false;
                    res["result"].forEach(function(v:any,i:any,a:any){
                        a[i]["gender"] = "resource/img/course_icon/"+a[i]["gender"]+"g.png";
                    });
                    this.pyqs = this.pyqs.concat(res["result"]);
                    if(this.currentPage==1){
                        this.endOfPageDetect();
                    }
                }
                else{
                    this.page_loading=false;
                    window.removeEventListener("scroll");
                }
            }
        )
    }
    getCommentedForums(id:string,page:number){
        this.userService.getForumListOfSpecificUserToCommentWithJson(id,page).subscribe(
            res=>{
                if (res["code"]==1){
                    this.page_loading=false;
                    res["result"].forEach(function(v:any,i:any,a:any){
                        a[i]["gender"] = "resource/img/course_icon/"+a[i]["gender"]+"g.png";
                    });
                    this.pyqs = this.pyqs.concat(res["result"]);
                    if(this.currentPage==1){
                        this.endOfPageDetect();
                    }
                }
                else{
                    this.page_loading=false;
                    window.removeEventListener("scroll");
                }
            }
        )
    }
    delForum(event:Event,index:number,id:string){
        event.stopPropagation();
        this.forumService.deleteForumWithJson(id).subscribe(
            res=>{
                if(res["code"]==1){
                    this.pyqs.splice(index,1);
                }
            },
            error=>{

            }
        )
    }
    endOfPageDetect(){
        window.addEventListener("scroll",()=>{
                let h = document.body.offsetHeight;
                if(((window.innerHeight + window.pageYOffset) > h - 1000) && ((window.innerHeight + window.pageYOffset) < h) && (this.page_loading == false)){
                    this.page_loading=true;
                    this.currentPage++;
                    this.getForums(this.cookieService.get("cc_id"),this.currentPage);
                }
            }
        )
    }
    ngOnDestroy(){
        window.removeEventListener("scroll");
    }
    sanitizeStyle(URL: string) {
        return this.sanitizer.bypassSecurityTrustStyle("URL('" + URL + "')");
    }
}


