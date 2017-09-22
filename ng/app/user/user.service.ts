import {Injectable} from "@angular/core";
import {Headers,Http} from "@angular/http";
import "rxjs/add/operator/map";
import {Subject} from 'rxjs/Subject';
@Injectable()
export class UserService {
    user_info:Object;
    user_info_observable:Subject<Object> = new Subject<Object>();
    private h:Headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});
    private url = "admin/user/userController.php?action=";
    constructor(private http: Http) {
    }


    getRowOfUserBasicInfoWithJson(id:string){
        return this.http.get(this.url+"getRowOfUserBasicInfoWithJson&userId="+id).map(
            res=>{
                return res.json()
            }
        )
    }
    getRowOfUserWithJson(id:string){
        return this.http.get(this.url+"getRowOfUserWithJson&userId="+id).map(
            res=>{
                return res.json()
            }
        )
    }
    updatePwd(data:string){
        return this.http.post(this.url+"updatePwd",data,{headers:this.h}).map(
            res=>{
                return res.json()
            }
        )
    }
    updateAliasWithJson(data:string) {
        return this.http.post(this.url + "updateAliasWithJson",data,{headers:this.h}).map(
            res => {
                return res.json()
            }
        )
    }

    userUpdateHeadImgWithJson(fd:FormData){
        return this.http.post(this.url+"userUpdateHeadImgWithJson",fd).map(
            res=>{
                return res.json()
            }
        )
    }
    getForumListOfSpecificUserWithJson(id:string,page:number){
        return this.http.get("admin/forum/forumController.php?action=getForumListOfSpecificUserWithJson&forum_class_id=0&userId="+id+"&page="+page).map(
            res=>{
                return res.json()
            }
        )
    }
    getForumListOfSpecificUserToCommentWithJson(id:string,page:number){
        return this.http.get("admin/forum/forumController.php?action=getForumListOfSpecificUserToCommentWithJson&forum_class_id=0&userId="+id+"&page="+page).map(
            res=>{
                return res.json()
            }
        )
    }
    getCourseListSpecificToUserCommentWithJson(id:string){
        return this.http.get("admin/course/courseController.php?action=getCourseListSpecificToUserCommentWithJson&userId="+id).map(
            res=>{
                return res.json()
            }
        )
    }


}