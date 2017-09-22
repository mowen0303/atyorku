import {Injectable} from "@angular/core";
import {Http,Headers,RequestOptions} from "@angular/http"
import "rxjs/add/operator/map";
import {Subject} from 'rxjs/Subject';

@Injectable()
export class LoginService{
    constructor(private http:Http){

    }

    logged_in :boolean;
    private header:Headers = new Headers({'Content-Type':'application/x-www-form-urlencoded'});
    login_hide_change : Subject<Object> = new Subject<Object>();


    login(data:string){
       return this.http.post("admin/login/loginController.php?action=userLogin",data,{headers:this.header}).map(res=>res.json());
    }
    logOff(){
        return this.http.get("admin/login/loginController.php?action=loginOut").map(res=>{});
    }
    register(data:string){
        return this.http.post("admin/user/userController.php?action=userRegisterWithJson",data,{headers:this.header}).map(res=>res.json());
    }

}