import {Component,animate,trigger,state,transition,keyframes,style} from "@angular/core";
import {ActivatedRoute} from "@angular/router";
import {DomSanitizer} from "@angular/platform-browser";
import {ForumService} from "./forum.service";
import { CookieService } from 'angular2-cookie/services/cookies.service';
@Component({
    templateUrl:"ng/app/forum/forumdetail.component.html",
    styleUrls:["ng/app/forum/forumdetail.component.css"],
    animations:[
        trigger("flyInOut",[
                state("*",style({
                    transform:"translateX(0)"
                })),
                transition(":leave",[animate("400ms ease-out",keyframes([
                    style({
                        transform:"translateX(0)",
                        offset:0
                    }),
                    style({
                        transform:"translateX(-100px)",
                        offset:0.8
                    }),
                    style({
                        transform:"translateX(1000px)",
                        offset:1.0
                    })
                ]))
                ]),
                transition(":enter",[animate("400ms ease-out",keyframes([
                    style({
                        transform:"translateX(1000px)",
                        offset:0
                    }),
                    style({
                        transform:"translateX(0)",
                        offset:1.0
                    })
                ]))
                ])
            ]
        )]
})

export class ForumDetailComponent {
    page_loading:boolean = false;
    pyq_id:string;
    user_comment:string;
    pyq:Object;
    comments:Object[]=[];
    current_page:number;
    comment_button_disable:boolean=false;
    constructor(private forumService: ForumService, private route: ActivatedRoute,private cookieService:CookieService,private sanitizer:DomSanitizer) {
    }
    ngOnInit(){
        this.route.params.subscribe(
                res=>{
                    this.page_loading=false;
                    this.current_page=1;
                    this.pyq_id = res["id"];
                    window.scrollTo(0,0);
                    if(this.forumService.current_pyq){
                        this.pyq = this.forumService.current_pyq;
                    }
                    else{
                        this.forumService.getOneForumAndCommentWithJson(this.pyq_id).subscribe(
                            res=>{
                                this.pyq=res["result"];
                                this.pyq["gender"]="resource/img/course_icon/"+this.pyq["gender"]+"g.png";
                                this.forumService.current_forum_id=this.pyq["forum_class_id"];
                            },
                            error=>{
                            }
                        )
                    }
                    console.log(this.forumService.current_forum_id);
                    this.forumService.countViewOfForumByIdWithJson(this.pyq_id).subscribe(
                        res=>{},
                        error=>{console.log(error)}
                    );
                    this.getForumCommentListWithJson(this.pyq_id,this.current_page);
                },
                error=>{
                    console.log("something is wrong")
            }
        )
    }
    addComment(id:string,comment:string){
        if(this.user_comment) {
            this.comment_button_disable=true;
            let user_id = this.cookieService.get("cc_id");
            this.forumService.addCommentWithJson(id, comment, user_id).subscribe(
                res => {
                    this.comment_button_disable=false;
                    if (res["code"] == 1) {
                        this.comments.unshift(this.createNewComment(comment, res["result"]["insertId"]));
                        this.user_comment = "";
                    }
                },
                error => {
                }
            )
        }
    }
    createNewComment(comment:string,id:string):Object{
        let new_comment = {
            course_class_id:this.pyq_id,
            id:id,
            user_id:this.cookieService.get("cc_id"),
            content_comment:comment,
            time:"刚刚发布",
            alias:this.cookieService.get("cc_al"),
            img:this.cookieService.get("cc_im"),
            gender:"resource/img/course_icon/"+this.cookieService.get("cc_ge")+"g.png",
            editable:"yes",
            major:this.cookieService.get("um"),
            enroll_year:this.cookieService.get("uy")
        }
       return new_comment;
    }
    delForum(id:string){
        this.forumService.deleteForumWithJson(id).subscribe(
            res=>{
                if(res["code"]==1){
                    history.back();
                }
            },
            error=>{

            }
        )
    }
    getForumCommentListWithJson(id:string,page:number){
        this.forumService.getForumCommentListWithJson(id,page).subscribe(
            res=>{
                if(res["code"]==1) {
                    this.page_loading=false;
                    res["secondResult"].forEach(function (v:any, i:any, a:any) {
                        a[i]["gender"] = "resource/img/course_icon/" + a[i]["gender"] + "g.png";
                    });
                    this.comments = this.comments.concat(res["secondResult"]);
                    if(this.current_page==1){
                        this.endOfPageDetect();
                    }
                }
                else{
                    window.removeEventListener("scroll");
                    this.page_loading=false;
                }
            },
            error=>{

            }
        )
    }
    delComment (comment_id:string,i:number){
        this.forumService.deleteCommentWithJson(comment_id).subscribe(
            res=>{
                if(res["code"] == 1){
                    this.comments.splice(i,1);
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
                    this.current_page++;
                    this.getForumCommentListWithJson(this.pyq["id"],this.current_page);
                }
            }
        )
    }
    sanitizeStyle(URL: string) {
        return this.sanitizer.bypassSecurityTrustStyle("URL('" + URL + "')");
    }
    ngOnDestroy(){
        window.removeEventListener("scroll");
    }
}


