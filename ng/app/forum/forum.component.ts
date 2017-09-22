import {Component} from "@angular/core";
import {ForumService} from "./forum.service";
@Component({
    templateUrl:"./ng/app/forum/forum.component.html",
    styleUrls:["./ng/app/forum/forum.component.css"],
    selector:"forum",
    })

export class ForumComponent{

    forums : Object[];
    add:String="resource/img/forum_icon/";
    icons:String[]=[this.add+"1-gray.png",this.add+"2-gray.png",this.add+"3-gray.png",this.add+"4-gray.png",this.add+"tutor-gray.png",
        this.add+"6-gray.png",this.add+"7-gray.png",this.add+"8-gray.png"];

    constructor(private forumService:ForumService){}

    ngOnInit(){
        this.getForumCsLlasistWithJson();

    }

    getForumCsLlasistWithJson(){
        this.forumService.getForumCsLlasistWithJson().subscribe(
            res=>{
                this.forums = res["result"];
                this.forumService.forum_classes = this.forums;
            },
            error=>{

            }
        )
    }

}