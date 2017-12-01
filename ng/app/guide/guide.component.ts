import {Component,animate,trigger,state,transition,keyframes,style} from "@angular/core";
import {GuideService} from "./guide.service";
@Component({
    templateUrl:"./ng/app/guide/guide.component.html",
    styleUrls:["./ng/app/guide/guide.component.css"],
    selector:"course"
})

export class GuideComponent{
    add:string="resource/img/guide_icon/";
    icons:string[]=[this.add+"1-gray.png",this.add+"2-gray.png",this.add+"3-gray.png",this.add+"4-gray.png",this.add+"5-gray.png",
        this.add+"6-gray.png",this.add+"7-gray.png",this.add+"2-gray.png"];
    guides:Object[];

    constructor(private guideService:GuideService){}

    ngOnInit(){
        this.guideService.getGuideClassListVisibleWithJson().subscribe(
            res=>{
                this.guides= res["result"];
            },
            error=>{

            }
        );

    }

}


