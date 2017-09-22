import {Routes,RouterModule} from "@angular/router";
import {CourseComponent} from "./course/course.component";
import {CoursesComponent} from "./course/courses.component";
import {CourseDetailComponent} from "./course/coursedetail.component";
import {GuideComponent} from "./guide/guide.component";
import {GuidesComponent} from "./guide/guides.component";
import {ForumComponent} from "./forum/forum.component";
import {ForumsComponent} from "./forum/forums.component";
import {ForumDetailComponent} from "./forum/forumdetail.component";
import {AuthGuard} from "./authguard";
import {UserComponent} from "./user/user.component";
import {UserForumComponent} from "./user/userforums.component";
import {UserCourseComponent} from "./user/usercourse.component";
import {UserProfileComponent} from "./user/userprofile.component";
import {OtherUserComponent} from "./user/otheruser.component";
import {GuideDetailComponent} from "./guide/guidedetail.component"
let routes:Routes = [
    {
        path :"",
        redirectTo:"guide",
        pathMatch:"full"
    },
    {
        path:"course",
        component:CourseComponent,
        children:[
                {
                path:":c_id",
                component:CoursesComponent
            },
            {
                path: "detail/:id",
                component: CourseDetailComponent
            },
            {
                path:"",
                redirectTo:"1",
                pathMatch:"full"
            }
        ],
        canActivate:[AuthGuard]
    },
    {
        path:"guide",
        component:GuideComponent,
        children:[
            {
                path:":id",
                component:GuidesComponent
            },
            {
                path:"",
                redirectTo:"0",
                pathMatch:"full"
            },
            {
                path:"detail/:guide_id",
                component:GuideDetailComponent
            }

        ]
    },
    {
        path:"forum",
        component:ForumComponent,
        children:[
            {
                path:":id",
                component:ForumsComponent
            },
            {
                path:"",
                redirectTo:"0",
                pathMatch:"full"
            },
            {
                path:"detail/:id",
                component:ForumDetailComponent
            }
        ],
        canActivate:[AuthGuard]
    },
    {
        path:"user",
        component:UserComponent,
        canActivate:[AuthGuard],
        children:[
            {
                path:"view/:action",
                component:UserForumComponent
            },
            {
                path:"",
                redirectTo:"profile",
                pathMatch:"full"
            },
            {
                path:"commentedcourses",
                component:UserCourseComponent
            },
            {
                path:"profile",
                component:UserProfileComponent
            }
        ]
    },
    {
        path:"otheruser/:id",
        component:OtherUserComponent
    }

];





export const routing = RouterModule.forRoot(routes);