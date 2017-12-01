import { NgModule }      from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { AppComponent }  from './app.component';
import {CourseService} from "./course/course.service";
import {CoursesComponent} from "./course/courses.component";
import {CourseComponent} from "./course/course.component";
import {CourseDetailComponent} from "./course/coursedetail.component";
import {routing} from "./app.router";
import {HttpModule} from "@angular/http";
import {FormsModule} from "@angular/forms";
import {LoginService} from "./login.service";
import {GuideComponent} from "./guide/guide.component";
import {GuideService} from "./guide/guide.service";
import {GuidesComponent} from "./guide/guides.component";
import {ForumComponent} from "./forum/forum.component";
import {ForumService} from "./forum/forum.service";
import {ForumsComponent} from "./forum/forums.component";
import { CookieService } from 'angular2-cookie/services/cookies.service';
import {ForumDetailComponent} from "./forum/forumdetail.component";
import {AuthGuard} from "./authguard";
import {UserService} from "./user/user.service";
import {UserComponent} from "./user/user.component";
import {UserForumComponent} from "./user/userforums.component";
import {UserCourseComponent} from "./user/usercourse.component";
import {UserProfileComponent} from "./user/userprofile.component";
import {OtherUserComponent} from "./user/otheruser.component";
import {LocationStrategy,HashLocationStrategy} from "@angular/common";
import {GuideDetailComponent} from "./guide/guidedetail.component";
@NgModule({
  imports:      [ BrowserModule,routing,HttpModule,FormsModule ],
  declarations: [ AppComponent,CoursesComponent,CourseComponent,CourseDetailComponent,GuideComponent,GuidesComponent,ForumComponent,ForumsComponent,ForumDetailComponent,UserComponent,
    UserForumComponent,UserCourseComponent,UserProfileComponent,OtherUserComponent,GuideDetailComponent],
  bootstrap:    [ AppComponent],
  providers: [CourseService,LoginService,GuideService,ForumService,CookieService,AuthGuard,UserService,{provide:LocationStrategy,useClass:HashLocationStrategy}]
})
export class AppModule { }
