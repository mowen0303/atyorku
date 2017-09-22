import {Component,HostListener} from "@angular/core";
import {GuideService} from "./guide.service";
import {ActivatedRoute,Params} from "@angular/router";
import {isUndefined} from "util";
import {isNullOrUndefined} from "util";
import {DomSanitizer} from "@angular/platform-browser";



@Component({
    selector: "courses",
    templateUrl : "./ng/app/guide/guidedetail.component.html",
    styleUrls: ["./ng/app/guide/guidedetail.component.css"]

})
export class GuideDetailComponent{
    currentPage:number;
    guide_id:string;
    iframe:Object;
    constructor(private guideService:GuideService,private route:ActivatedRoute,private sanitizer:DomSanitizer){

    }
    ngOnInit(){
        this.route.params.subscribe(
            res=>{
                this.iframe={};
                this.guide_id=res["guide_id"];
                this.loadGuide(this.guide_id);
            },
            error=>{
            }
        )
    }

    sanitizeUrl(URL:string){
        return this.sanitizer.bypassSecurityTrustResourceUrl(URL);
    }
    loadGuide(id:string){
        this.iframe["iframeURL"] =this.sanitizeUrl('apps/guide/index.php?guide_id='+id);
        this.iframe["loadIframe"]=true;
    }
    onLoad(){
        let iframe   = document.getElementById('iframe');
        let doc = iframe['contentDocument']|| iframe['contentWindow']['document'];
        iframe['height']=doc.body.scrollHeight;
    }

}/**
 * Created by XIN on 2017/2/4.
 */
