import {Component} from "@angular/core";
import {CourseService} from "./course.service";
import {ActivatedRoute,Router} from "@angular/router";


@Component({
    selector: "courses",
    templateUrl : "./ng/app/course/courses.component.html",
    styleUrls: ["./ng/app/course/courses.component.css"]

})
export class CoursesComponent{
    c_id:string;
    courses:Object[];
    constructor(private courseService:CourseService, private router:Router,private route:ActivatedRoute){}

    ngOnInit(){
        this.route.params.subscribe(
            res => {
                this.c_id = res["c_id"];
                window.scrollTo(0,0);
                this.courseService.getCourseListWithJson(this.c_id).subscribe(
                    res => {
                        this.courses = res.result;
                        this.courses.forEach((v,i,a)=>{
                            a[i]["diff"] = "resource/img/course_icon/"+Math.floor(+a[i]["diff"])+".png";
                        });
                        this.courseService.current_course_class_id=this.c_id;
                    },
                    error=>{}
                )
            },
            error => {}
        )
    }

    navi(id:String){
        this.router.navigate(["/course/detail",id]);
    }



}