import {Component,HostListener} from "@angular/core";
import {GuideService} from "./guide.service";
import {ActivatedRoute,Params} from "@angular/router";
import {isUndefined} from "util";
import {isNullOrUndefined} from "util";
import {DomSanitizer} from "@angular/platform-browser";



@Component({
    selector: "courses",
    templateUrl : "./ng/app/guide/guides.component.html",
    styleUrls: ["./ng/app/guide/guides.component.css"]

})
export class GuidesComponent{
    currentPage:number;
    guide_class_id:string;
    guides:Object[];
    page_loading:boolean=false;
    iframe:Object;
    constructor(private guideService:GuideService,private route:ActivatedRoute,private sanitizer:DomSanitizer){

    }
    ngOnInit(){
        this.route.params.subscribe(
            res=>{
                this.iframe={};
                window.removeEventListener("scroll");
                this.page_loading=false;
                this.guide_class_id=res["id"];
                this.guideService.current_guide_id=this.guide_class_id;
                this.currentPage=1;
                this.guideService.getGuideListWithJson(this.guide_class_id,this.currentPage).subscribe(
                   res=>{
                       this.guides=res["result"];
                       window.scrollTo(0,0);
                       this.endOfPageDetect();
                   },
                   error=>{

                   }
                )

            }
            ,
            error=>{

            }
        )
    }

    endOfPageDetect(){
        window.addEventListener("scroll",()=>{
                let h = document.body.offsetHeight;
                if(((window.innerHeight + window.pageYOffset) > h - 1000) && ((window.innerHeight + window.pageYOffset) < h) && (this.page_loading == false)){
                        this.page_loading=true;
                        this.currentPage++;
                        this.guideService.getGuideListWithJson(this.guide_class_id,this.currentPage).subscribe(
                            res=>{
                                if(res["code"]==1){
                                    this.guides = this.guides.concat(res["result"]);
                                    this.page_loading=false;
                                }
                                else{
                                    window.removeEventListener("scroll");
                                    this.page_loading=false;
                                }
                            },
                            error=>{

                            }
                        )
                        }
            }
        )
    }

    ngOnDestroy(){
        window.removeEventListener("scroll");
    }
    sanitizeStyle(URL: string) {
        return this.sanitizer.bypassSecurityTrustStyle("URL('" + URL + "')");
    }

    sanitizeUrl(URL:string){
        return this.sanitizer.bypassSecurityTrustResourceUrl(URL);
    }


}