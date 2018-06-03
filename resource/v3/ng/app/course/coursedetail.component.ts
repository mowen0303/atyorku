import {Component,animate,trigger,state,transition,keyframes,style} from "@angular/core";
import {ActivatedRoute, Router} from "@angular/router";
import {CourseService} from "./course.service";
import { CookieService } from 'angular2-cookie/services/cookies.service';
import {DomSanitizer} from "@angular/platform-browser";
@Component({
    selector: "course-detail",
    templateUrl:"ng/app/course/coursedetail.component.html",
    styleUrls:["ng/app/course/coursedetail.component.css"],
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

export class CourseDetailComponent {
    currentPage:number;
    user_comment:string;
    private id:string;
    config:Object;
    course: Object[];
    comments: Object[];
    page_loading:boolean;
    comment_button_disable:boolean = false;
    constructor(private courseService: CourseService, private cookieService:CookieService,private route: ActivatedRoute,private sanitizer:DomSanitizer,private router:Router) {
    }


    ngOnInit(){
        this.showSummary();
        this.page_loading=false;
        this.currentPage = 1;
        this.route.params.subscribe(
            res => {this.id = res["id"];
                this.courseService.getCourseDetailByIdWithJson(this.id).subscribe(
                    res=> {
                        this.course = res.result;
                        this.courseService.current_course=this.course;
                        this.comments = res.secondResult;
                        this.course["diff"] = "resource/img/course_icon/"+Math.floor(this.course["diff"])+".png";
                        this.comments.forEach(function(v,i,a){
                            a[i]["gender"] = "resource/img/course_icon/"+a[i]["gender"]+"g.png";
                        });
                    },
                    error=>{}
                )
            },
            error => {}
        )
    }

    ngOnDestroy(){
        window.removeEventListener("scroll");
    }

    detectEndOfPage(){
        window.addEventListener("scroll",()=>{
            let h = document.body.offsetHeight;
            if(((window.innerHeight + window.pageYOffset) > h - 1000) && ((window.innerHeight + window.pageYOffset) < h) && (this.page_loading == false)){
                this.currentPage++;
                this.page_loading=true;
                this.getComment(this.id,this.currentPage);
            }
        })
    }


    addComment(comment:string){
       if(this.user_comment){
           this.comment_button_disable=true;
           this.courseService.addCommentWithJson(this.id,comment).subscribe(
                res=>{
                    this.comment_button_disable=false;
                    this.user_comment="";
                    if(res["code"]==1){
                        this.comments.unshift(this.createNewComment(comment,res["result"]["insertId"]));
                    }
                },
                error=>{
                }
            )
       }
    }
    createNewComment(comment:string,id:string):Object{
        let new_comment = {
            course_class_id:this.id,
            id:id,
            user_id:this.cookieService.get("cc_id"),
            comment:comment,
            time:"刚刚发布",
            alias:this.cookieService.get("cc_al"),
            uid:this.cookieService.get("cc_id"),
            img:this.cookieService.get("cc_im"),
            gender:"resource/img/course_icon/"+this.cookieService.get("cc_ge")+"g.png",
            editable:"yes"
        }
        return new_comment;
    }
    delComment (comment_id:string,i:number){
        this.courseService.delCommentWithJson(comment_id,this.id).subscribe(
            res=>{
                if(res["code"] == 1){
                    this.comments.splice(i,1);
                }
            },
            error=>{

            }
        )
    }

    getComment(id:string,page:number){
        this.courseService.getCourseCommentByIdWithJson(id,page).subscribe(
            res=>{
                if(res["code"]==1){
                    let temp_comments = res["secondResult"];
                    temp_comments.forEach(function(v:any,i:any,a:any){
                        a[i]["gender"] = "resource/img/course_icon/"+a[i]["gender"]+"g.png";
                    });
                    this.comments = this.comments.concat(temp_comments);
                    this.page_loading=false;
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
    sanitizeStyle(URL: string) {
        return this.sanitizer.bypassSecurityTrustStyle("URL('" + URL + "')");
    }
    navi(id:string){
        if(this.cookieService.get("cc_id") == id){
            this.router.navigate(["/user/profile"]);
        }
        else{
            this.router.navigate(["/otheruser",id]);
        }
    }

    showInfo(){
            this.config = {
            comment : true,
            summary : true,
            info : false
        };
        window.removeEventListener("scroll");
        this.page_loading=false;
    }
    showSummary(){
        this.config = {
            comment : true,
            summary : false,
            info : true
        };
        window.removeEventListener("scroll");
        this.page_loading=false;
    }
    showComment(){
        this.config = {
            comment : false,
            summary : true,
            info : true
        };
        this.page_loading=false;
        this.detectEndOfPage();

    }
}

