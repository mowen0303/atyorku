import {Component,animate,trigger,state,transition,keyframes,style} from "@angular/core";
import {CourseService} from "./course.service";
@Component({
    templateUrl:"./ng/app/course/course.component.html",
    styleUrls:["./ng/app/course/course.component.css"],
    selector:"course"
})

export class CourseComponent{

    courseClass : Object[];

    constructor(private courseService:CourseService){}

    ngOnInit(){
        this.courseService.getListOfFatherClassWithJson().subscribe(
            (res:Object) => {this.courseClass = res["result"];

            },
            error => {}
        );

    }

}