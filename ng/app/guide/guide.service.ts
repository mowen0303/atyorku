import {Injectable} from "@angular/core";
import {Headers,Http} from "@angular/http";
import {Observable} from "rxjs";
import "rxjs/add/operator/map";
@Injectable()
export class GuideService {
    private url = "admin/guide/guideController.php?action=";
    current_guide_id:String;
    constructor(private http: Http) {
    };
    getGuideClassListVisibleWithJson(){
        return this.http.get(this.url+"getGuideClassListVisibleWithJson").map(res => {
            return res.json()
        })
    }

    getGuideListWithJson(id:string,page:number){
        return this.http.get(this.url+"getGuideListWithJson&guide_class_id="+id+"&page="+page).map(res => {
            return res.json()
        })
    }


}
