import {Component,Input,OnInit,trigger, state,
  style,
  transition,
  animate,} from "@angular/core";
import {LoginService} from "./login.service";
import {CourseService} from "./course/course.service";
import { CookieService } from 'angular2-cookie/services/cookies.service';
import {Router} from "@angular/router";
import {DomSanitizer} from "@angular/platform-browser";



@Component(
    {
      templateUrl:'ng/app/app.component.html',
      styleUrls:["ng/app/app.component.css"],
      selector:"my-app",
      animations:[
        //mobile menu effect
        trigger("menu_click",[
          state("inactive",style({
            transform:"rotate(0)"
          })),
          state("active",style({
            transform:"rotate(90deg)"
          })),
          transition("inactive <=> active",animate("300ms ease-out"))
        ]),
        trigger("menu_dropdown",[
          state("inactive",style({
            fontSize:"0",
            height:"0"
          })),
          state("active",style({
            fontSize:"1rem",
            height:"100vh"
          })),
          transition("inactive<=>active",animate("400ms ease-out"))
        ]),
        //mobile menu effect

      ]
    }
)
export class AppComponent{
  navigate_url:string;
  register_error:boolean;
  register_password_error:boolean;
  login_error:boolean;
  register_error_message:string;
  login_hide:boolean = true;
  register_hide:boolean=true;
  letter_grades = this.courseService.letter_grades;
  constructor(private loginService:LoginService,private courseService:CourseService,private cookieService:CookieService,private router:Router,private sanitizer:DomSanitizer){

  }
  ngOnInit(){

    if (this.cookieService.get("cc_id")){
      this.loginService.logged_in=true;
    }
    else{
      this.loginService.logged_in=false;
    }
      this.loginService.login_hide_change.subscribe(
          res=>{
              this.login_hide=res["login_hide"];
              this.navigate_url=res["url"];
          }
      );
  }

  //mobile menu effect states and toggler
  menu_click = "inactive";
  menu_click_toggle(){
    if(this.menu_click === "inactive"){
      this.menu_click = "active";

    }
    else{
      this.menu_click = "inactive";

    }
  }
  menu_click_toggle_onresize(){
    if(window.innerWidth >= 767 && this.menu_click === "active") {
      this.menu_click = "inactive";
    }
  }
  // mobile menu effect states and toggler
  addRate(){
    this.courseService.addRateWithJson(this.courseService.rate_class_id,this.courseService.selected_star_grade,this.courseService.selected_letter_grade).subscribe(
        res=>{
          if(res["code"]==1){
            this.courseService.current_course["rateAllowed"]="no";
            this.courseService.current_course["average_count"]=+this.courseService.current_course["average_count"] + 1;
          }
        },
        error=>{
        }
    );
    this.courseService.close_rate_window();
  }

  getCookie(name:string):string{
    return this.cookieService.get(name);
  }

  showLogin(){
    this.login_hide = false;
  }
  hideLogin(form:any){
    this.login_hide= true;
    form.reset();
  }
  showRegister(){
      this.register_hide = false;
  }
  hideRegister(form:any){
      this.register_hide=true;
      form.reset();
  }
  login(form:any){
    this.clear_error();
    if(form.valid){
        let data = "username="+form.value["username"]+"&password="+form.value["password"];
        this.loginService.login(data).subscribe(
            res=>{
              if(res["code"]==1){
                this.loginService.logged_in=true;
                this.hideLogin(form);
                this.hideRegister(form);
                if(this.navigate_url){;
                        this.router.navigate([this.navigate_url]);
                        this.navigate_url="";
                }
              }
              else{
                this.login_error=true;
              }
            },
            error=>{
              console.log(error);
            }
        )
    }
  }
  logOff(){
    this.loginService.logOff().subscribe(
        res=>{
            if(!this.router.url.includes("guide")){
                this.router.navigate(["/guide"]);
            }
        },
        error=>{
        }
    );
    this.loginService.logged_in=false;
  };
  register(form:any){
    this.clear_error();
    if(form.valid){
        if(form.value["register_password"] != form.value["check_password"]){
            this.register_password_error=true;
        }
        else{
            let data = "username="+form.value["register_username"]+"&password="+form.value["register_password"]+"&password2="+form.value["register_password"];

            this.loginService.register(data).subscribe(
                res=>{
                    if(res["code"]==1){
                        form.value['username']=form.value["register_username"];
                        form.value["password"]=form.value["register_password"];
                        this.login(form);
                    }
                    else{
                        this.register_error=true;
                        this.register_error_message = res["message"];
                    }
                },
                error=> {
                    console.log(error);
                }
            )
        }

        }
    }

  clear_error(){
    this.login_error=false;
    this.register_error=false;
    this.register_password_error=false;
  }
    sanitizeStyle(URL: string) {
        return this.sanitizer.bypassSecurityTrustStyle("URL('" + URL + "')");
    }
}

