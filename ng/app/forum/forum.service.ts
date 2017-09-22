import {Injectable} from "@angular/core";
import {Headers,Http,RequestOptions} from "@angular/http";
import {Observable} from "rxjs";
import "rxjs/add/operator/map";
@Injectable()
export class ForumService {
    forum_classes:Object[];
    current_pyq:Object;
    current_forum_id:string = "0";
    private h:Headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});
    private url = "admin/forum/forumController.php?action=";
    category:string;
    pyqs_cache = {};
    set_buy(){
        this.category = "buy";
    }
    set_sell(){
        this.category="sell"
    }
    constructor(private http: Http) {
    }

    getOneForumAndCommentWithJson(id:string){
        return this.http.get(this.url+"getOneForumAndCommentWithJson&forum_id="+id).map(res => {
            return res.json()
        });
    }

    getForumCsLlasistWithJson(){
        return this.http.get(this.url+"getForumClassListWithJson").map(res => {
            return res.json()
        });
    }

    getForumListWithJson(id:string,page:number){
        return this.http.get(this.url+"getForumListWithJson&forum_class_id="+id+"&page="+page).map(res => {
            return res.json()
        });
    }
    deleteForumWithJson(id:string){
        let data="id="+id;
        return this.http.post(this.url+"deleteForumWithJson",data,{headers:this.h}).map(res => {
            return res.json()
        });
    }
    addForumWithJson(fd:FormData){
        return this.http.post(this.url+"addForumWithJson",fd).map(res=>{
            return res.json();
        })
    }
    addCommentWithJson(forum_id:string,comment:string,user_id:string){
        let data = "user_id="+user_id+"&forum_id="+forum_id+"&content_comment="+comment;
        return this.http.post(this.url+"addCommentWithJson",data,{headers:this.h}).map(res => {
            return res.json()
        });

    }
    getForumCommentListWithJson(id:string,page:number){
        return this.http.get(this.url+"getForumCommentListWithJson&forum_id="+id+"&page="+page).map(res=>{
            return res.json();
        })
    }

    deleteCommentWithJson(id:string){
        return this.http.get(this.url+"deleteCommentWithJson&id="+id).map(res=>{
            return res.json();
        })
    }

    countViewOfForumByIdWithJson(id:string){
        return this.http.get(this.url+"countViewOfForumByIdWithJson&id="+id).map(res=>{
            return res.json();
        })
    }
    cachePyqs(pyqs:Object,page:number,id:String){
        this.pyqs_cache['page']=page;
        this.pyqs_cache['pyqs']=pyqs;
        this.pyqs_cache['id']=id;
    }
}