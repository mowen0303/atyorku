import {Component,animate,trigger,state,transition,keyframes,style} from "@angular/core";
import {DomSanitizer} from "@angular/platform-browser";
import {ActivatedRoute,Router} from "@angular/router";
import {ForumService} from "./forum.service";
import { CookieService } from 'angular2-cookie/services/cookies.service';

@Component({
    templateUrl:"ng/app/forum/forums.component.html",
    styleUrls:["ng/app/forum/forums.component.css"],
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
    }
)

export class ForumsComponent {
    pyqs: Object[];
    id: string;
    reload: boolean;
    page_loading: boolean = false;
    hidden_buy_price: boolean;
    current_page: number = 1;
    button_disable: boolean = false;
    posting_error:boolean;
    posting_error_message:string;
    constructor(private forumService: ForumService, private route: ActivatedRoute, private cookieService: CookieService, private sanitizer: DomSanitizer,private router:Router) {
    }

    ngOnInit() {
        this.route.params.subscribe(
            res => {
                window.scrollTo(0,0);
                this.posting_error=false;
                window.removeEventListener("scroll");
                this.current_page = 1;
                this.page_loading = false;
                this.reload = false;
                this.id = res["id"];
                if(this.forumService.pyqs_cache && this.forumService.pyqs_cache["id"] == this.id){
                    this.pyqs = this.forumService.pyqs_cache["pyqs"];
                    this.reload=true;
                    this.forumService.current_forum_id = this.id;
                    this.endOfPageDetect();
                }
                else {
                    this.forumService.getForumListWithJson(this.id, 1).subscribe(
                        res => {
                            this.pyqs = res["result"];
                            this.pyqs.forEach(function (v, i, a) {
                                a[i]["gender"] = "resource/img/course_icon/" + a[i]["gender"] + "g.png";
                            });
                            this.forumService.cachePyqs(this.pyqs,this.current_page,this.id);
                            this.forumService.current_forum_id = this.id;
                            this.reload = true;
                            this.endOfPageDetect();
                        },
                        error => {

                        }
                    )
                }
            },
            error => {

            }
        )
    }

    addForumWithJson(form: any, label: any, file: any) {
        this.posting_error=false;
        if (form.valid) {
            this.button_disable = true;
            if (this.hidden_buy_price||form.value["price"]==null) {
                form.value["price"] = "";
                form.value["category"] = "";
            }
            let fd = new FormData();
            fd.append("forum_class_id", form.value["forum_class_id"]);
            fd.append("price", form.value['price']);
            fd.append("content", form.value["content"]);
            fd.append("img1", file.files[0]);
            fd.append("category", this.forumService.category);
            fd.append("flag", "add");
            this.forumService.addForumWithJson(fd).subscribe(
                res => {
                    this.button_disable = false;
                    if (res["code"] == 1) {
                        this.createNewForum(form, label, res["result"]["insertId"]);
                        this.clear(form, label, file);
                    }
                    else{
                        this.posting_error=true;
                        this.button_disable=false;
                        this.posting_error_message=res["message"];
                    }
                },
                error => {
                    this.posting_error=true;
                    this.button_disable=false;

                }
            )
        }
    }

    createNewForum(form: any, label: any, id: string) {
        if (form.value["forum_class_id"] == this.id || this.id == "0") {
            let new_forum =
                {
                    gender: "resource/img/course_icon/" + this.cookieService.get("cc_ge") + "g.png",
                    alias: this.cookieService.get("cc_al"),
                    category: this.forumService.category,
                    comment_num: "0",
                    content: form.value["content"],
                    count_view: "0",
                    enroll_year: this.cookieService.get("cc_uy"),
                    editable: "yes",
                    forum_class_id: form.value["forum_class_id"],
                    img: this.cookieService.get("cc_im"),
                    img1: (label.dataset.src) ? label.dataset.src : "",
                    major: this.cookieService.get("cc_um"),
                    price: (form.value["price"] == "") ? "0" : form.value["price"],
                    time: "刚刚发布",
                    id: id,
                    user_id:this.cookieService.get("cc_id")
                };
            this.pyqs.unshift(new_forum);
            this.forumService.cachePyqs(this.pyqs,this.current_page,this.id);
        }
    }

    delForum(event:Event,index: number, id: string) {
        event.stopPropagation();
        this.forumService.deleteForumWithJson(id).subscribe(
            res => {
                if (res["code"] == 1) {
                    this.pyqs.splice(index, 1);
                    this.forumService.cachePyqs(this.pyqs,this.current_page,this.id);
                }
            },
            error => {

            }
        )
    }

    fileChangeListener(e: any, label: any) {
            let file = e.target.files[0];
            let reader = new FileReader();
            reader.onload = function (e) {
                let src = e.target['result'];
                label.style.backgroundImage = "url(" + src + ")";
                label.style.border = "none";
                label.style.backgroundSize = "cover";
                label.dataset.src = src;
            }
            reader.readAsDataURL(file);
    }

    categoryChange(e: Event) {
        if (e.target['value'] == "3" || e.target['value'] == "4" || e.target['value'] == "10") {
            this.hidden_buy_price = false;
            this.forumService.category = "buy";
        }
        else {
            this.hidden_buy_price = true;
            this.forumService.category = "";
        }
    }

    clear(form: any, label: any, file: any) {
        form.reset();
        file.value = null;
        this.button_disable = false;
        this.forumService.category = "";
        label.style.backgroundImage = "URL('resource/img/forum_icon/photo.png')";
        label.style.border = "dashed 1px lightgrey";
        label.style.backgroundSize = "40%";
        label.dataset.src = "";
        this.posting_error=false;
    }

    endOfPageDetect() {
        window.addEventListener("scroll", () => {
                let h = document.body.offsetHeight;
                if (((window.innerHeight + window.pageYOffset) > h - 1000) && ((window.innerHeight + window.pageYOffset) < h) && (this.page_loading == false)) {
                    this.page_loading = true;
                    this.current_page++;
                    this.getForums(this.id, this.current_page);
                }
            }
        )
    }

    ngOnDestroy() {
        window.removeEventListener("scroll");
    }

    getForums(id: string, page: number) {
        this.forumService.getForumListWithJson(this.id, this.current_page).subscribe(
            res => {
                if (res["code"] == 1) {
                    res["result"].forEach(function (v: any, i: any, a: any) {
                        a[i]["gender"] = "resource/img/course_icon/" + a[i]["gender"] + "g.png";
                    });
                    this.pyqs = this.pyqs.concat(res["result"]);
                    this.forumService.cachePyqs(this.pyqs,this.current_page,this.id);
                    this.page_loading = false;
                }
                else {
                    window.removeEventListener("scroll");
                    this.page_loading = false;
                }
            },
            error => {

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
}


