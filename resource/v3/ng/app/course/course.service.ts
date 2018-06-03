import {Injectable} from "@angular/core";
import {Headers,Http} from "@angular/http";
import {Observable} from "rxjs";
import "rxjs/add/operator/map";
@Injectable()
export class CourseService {
    rate_window = true;
    letter_grades:string[] = ["A+","A","B+","B","C+","C","D+","D","E","F"];
    selected_letter_grade:string;
    star_grades:string[]=["5","4","3","2","1"];
    selected_star_grade:string;
    rate_class_id:string;
    current_course_class_id:string ="1";
    private h:Headers = new Headers({"Content-Type":"application/x-www-form-urlencoded"});
    private url = "admin/course/courseController.php?action=";
    current_course:Object;
    constructor(private http: Http) {
    }
    close_rate_window(){
        this.rate_window=true;
        this.selected_letter_grade="";
        this.selected_star_grade="";
    }
    open_rate_window(){
        this.rate_window=false;
        this.selected_letter_grade="";
        this.selected_star_grade="";
    }

    getListOfFatherClassWithJson(): Observable<string[]> {
        return this.http.get(this.url + "getListOfFatherClassWithJson").map(res => {
            return res.json()
        });
    }

    getCourseListWithJson(c_id: string) {
        return this.http.get(this.url + "getCourseListWithJson&c_cid=" + c_id).map(res => {
            return res.json()
        });
    }

    getCourseDetailByIdWithJson(id:string){
        return this.http.get(this.url+"getCourseDetailByIdWithJson&courseId="+id+"&page=1").map(
            res=>{
                return res.json()
            }
        )
    }

    getCourseCommentByIdWithJson(id:string,page:number){
        return this.http.get(this.url+"getCourseCommentByIdWithJson&courseId="+id+"&page="+page).map(
            res=>{
                return res.json()
            }
        )
    }

    addCommentWithJson(id:string,comment:string){

        let data = "comment="+comment+"&course_class_id="+id;
        return this.http.post(this.url+"addCommentWithJson",data,{headers:this.h}).map(res => res.json());
    }

    delCommentWithJson (comment_id:string,class_id:string){
        let data = "id="+comment_id;
        return this.http.post(this.url+"delCommentWithJson&c_cid="+class_id,data,{headers:this.h}).map(res => res.json());

    }

    addRateWithJson(id:string,diff:string,grade:string){
        let data = "grade="+grade+"&diff="+diff;
        return this.http.post(this.url+"addRateWithJson&c_cid="+id,data,{headers:this.h}).map(res => res.json());
    }




}