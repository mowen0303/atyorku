import {Component} from "@angular/core";
import {UserService} from "./user.service";
import { CookieService } from 'angular2-cookie/services/cookies.service';
import {ForumService} from "../forum/forum.service";
import {ActivatedRoute} from "@angular/router";
import {DomSanitizer} from "@angular/platform-browser";
@Component({
    templateUrl:"./ng/app/user/userprofile.component.html",
    styleUrls:["./ng/app/user/userprofile.component.css"],
    selector:"user-profile"
})

export class UserProfileComponent{
    editInfo:Object;
    info:Object;
    editWechat:boolean=false;
    editAlias:boolean=false;
    editGender:boolean=false;
    editMajor:boolean=false;
    editYear:boolean=false;
    editDescription:boolean=false;
    profile_hidden:boolean;
    edit_profile_hidden:boolean;
    edit_password_hidden:boolean;
    edit_password_msg:string;
    edit_password_error_message:string;
    constructor(private userService:UserService,private cookieService:CookieService,private forumService:ForumService,private route: ActivatedRoute,private sanitizer:DomSanitizer){}

    ngOnInit(){
        this.editInfo={};
        this.edit_password_error_message="";
        this.edit_password_msg="";
        this.userService.user_info_observable.subscribe(
            res=>{this.info=res;
                this.showProfile()
            }
        );
        this.info=this.userService.user_info;
        this.showProfile();
    }
    updateAlias(alias:string){
        let data="userId="+this.cookieService.get("cc_id")+"&profile=alias&profileVal="+alias;
        this.userService.updateAliasWithJson(data).subscribe(
            res=>{
                if (res["code"]==1){
                    this.info["alias"]=alias;
                }
                this.editAlias=false;
            },
            error=>{console.log(error)}
        )
    }
    updateDescription(description:string){
        let data="userId="+this.cookieService.get("cc_id")+"&profile=description&profileVal="+description;
        this.userService.updateAliasWithJson(data).subscribe(
            res=>{
                if (res["code"]==1){
                    this.info["description"]=description;
                }
                this.editDescription=false;
            },
            error=>{console.log(error)}
        )
    }
    updateWechat(wechat:string){
        let data="userId="+this.cookieService.get("cc_id")+"&profile=wechat&profileVal="+wechat;
        this.userService.updateAliasWithJson(data).subscribe(
            res=>{
                if (res["code"]==1){
                    this.info["wechat"]=wechat;
                }
                this.editWechat=false;
            }
        )
    }
    updateMajor(major:string){
        let data="userId="+this.cookieService.get("cc_id")+"&profile=major&profileVal="+major;
        this.userService.updateAliasWithJson(data).subscribe(
            res=>{
                if (res["code"]==1){
                    this.info["major"]=major;
                }
                this.editMajor=false;
            },
            error=>{
                console.log(error);
            }
        )
    }
    updateGender(gender:string){
        let data="userId="+this.cookieService.get("cc_id")+"&profile=gender&profileVal="+gender;
        this.userService.updateAliasWithJson(data).subscribe(
            res=>{
                if (res["code"]==1){
                    this.info["gender"]="resource/img/课评icon/"+gender+"g.png";
                }
                this.editGender=false;
            },
            error=>{
                console.log(error);
            }
        )
    }

    updatePassword(form:any){
        this.edit_password_error_message="";
        this.edit_password_msg="";
        if (form.valid){
            let data="uid="+this.cookieService.get("cc_id")+"&pwd="+form.value["new_password"]+"&pwd2="+form.value["check_password"]+"&pwdOfOld="+form.value["original_password"];

            this.userService.updatePwd(data).subscribe(
                res=>{
                    console.log(res);
                    if(res["code"]==1){
                        this.edit_password_msg=res["message"];
                        this.showProfile();
                    }
                    else{
                        this.edit_password_error_message=res["message"];
                    }
                },
                error=>{
                }
            )
        }
    }


    imageChangeListener(e:any,image:any){
        let file=e.target.files[0];
        let reader = new FileReader();
        reader.onload = function(e){
            let src=e.target['result'];
            image.dataset.image=src;
        }
        reader.readAsDataURL(file);
        let fd = new FormData();
        fd.append("file",file);
        this.userService.userUpdateHeadImgWithJson(fd).subscribe(
            res=>{
                if(res["code"]==1){
                    let src=res["message"];
                    this.info["img"]=image.dataset.image;
                    this.cookieService.remove("cc_im");
                    document.cookie="cc_im="+src;
                }
            }
        )
    }
    edit_wechat(){
        this.editInfo["wechat"]=this.info["wechat"];
        this.editWechat=true;
    }
    edit_wechat_done(){
        if(this.editInfo["wechat"]==this.info["wechat"]){
            this.editWechat=false;
        }
        else {
            this.updateWechat(this.editInfo["wechat"]);
        }
    }
    edit_major(){
        this.editInfo["major"]=this.info["major"];
        this.editMajor=true;
    }
    edit_major_done(){
        if(this.editInfo["major"]==this.info["major"]){
            this.editMajor=false;
        }
        else {
            this.updateMajor(this.editInfo["major"]);
        }
    }
    edit_description(){
        this.editInfo["description"]=this.info["description"];
        this.editDescription=true;
    }
    edit_description_done(){
        if(this.editInfo["description"]==this.info["description"]){
            this.editDescription=false;
        }
        else {
            this.updateDescription(this.editInfo["description"]);
        }
    }
    edit_alias(){
        this.editInfo["alias"]=this.info["alias"];
        this.editAlias=true;
    }
    edit_alias_done(){
        if(this.editInfo["alias"]==this.info["alias"]){
            this.editAlias=false;
        }
        else {
            this.updateAlias(this.editInfo["alias"]);
        }
    }
    edit_gender(select:any){
        if(this.info["gender"].includes("0")){
            select.selectedIndex="1";
        }
        else{
            select.selectedIndex="0";
        }
        this.editGender=true;
    }
    edit_gender_done(select:any){
        if(this.info["gender"].includes(select.value)){
            this.editGender=false;
        }
        else {
            this.updateGender(select.value);
        }
    }
    sanitizeStyle(URL: string) {
        return this.sanitizer.bypassSecurityTrustStyle("URL('" + URL + "')");
    }
    showProfile(){
        this.edit_profile_hidden = true;
        this.profile_hidden=false;
        this.edit_password_hidden=true;
    }
    showEditProfile(){
        this.edit_profile_hidden = false;
        this.profile_hidden=true;
        this.edit_password_hidden=true;
        this.edit_password_msg="";
        this.edit_password_error_message="";
    }
    showEditPassword(){
        this.edit_profile_hidden = true;
        this.profile_hidden=true;
        this.edit_password_hidden=false;
        this.edit_password_msg="";
        this.edit_password_error_message="";
    }

}



