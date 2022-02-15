import { faTheRedYeti } from "@fortawesome/free-brands-svg-icons";

export default class Gate{
    constructor(user){
        this.user = user;
    }

    isAdmin(){
        return this.user.user_type === 'admin';
    }

    isAuthor(){
        return this.user.user_type === 'author';
    }

    isUser(){
        return this.user.user_type === 'user';
    }

    isAdminOrAuthor(){
     if(this.user.user_type === 'user' || this.user.user_type === 'author'){
        return true;
        };
    }
}
