/*********** Helpers Start ***********/
Array.prototype.first = function(){return this[0]};
Array.prototype.last = function(){return this[this.length - 1]};
/*********** Helpers End ***********/
/*********** Dates Start ***********/
now = new Date();
nowPlusTwoDays = now.setDate(now.getDate() + 2);
now = new Date(); //reinitialize
/*********** Dates End ***********/

var Auth = {
    token: localStorage.getItem("token"),
    check: function(){
        return this.token.length;
    },
    signIn: function(obj){
        showLoader();
        var form = $(obj).parent().parent().parent();
        $.ajax({
            type:"POST",
            url: "/api/login",
            data: form.serialize(),
            processData: false,
            success: function(msg){
                console.log(msg);
                if(msg.token.length > 1){
                    localStorage.setItem("token", msg.token);
                    localStorage.setItem("account", JSON.stringify(msg.account));
                    localStorage.setItem("children", JSON.stringify(msg.children));
                    localStorage.setItem("permissions", JSON.stringify(msg.permissions));
                    localStorage.setItem("roles", JSON.stringify(msg.roles));
                    localStorage.setItem("user", JSON.stringify(msg.user));
                    localStorage.setItem("leads_count", JSON.stringify(msg.leads_count));
                    if(localStorage.key('user'))
                        setTimeout(function(){window.location = '/';}, 2000);
                }
            },
            error: function(){
                hideLoader();
                $('.result').append('Invalid Credentials');
            }
        });
        return false;
    },
    register: function(obj){
        var form = $(obj).parent().parent().parent();
        $.ajax({
            type:"POST",
            url: "/api/signUp",
            data: form.serialize(),
            processData: false,
            success: function(msg){
                if(msg.token.length > 1){
                    localStorage.setItem("token", msg.token);
                    localStorage.setItem("user", JSON.stringify(msg.user));
                    localStorage.setItem("account", JSON.stringify(msg.account));
                    window.location = '/account/setup';
                }
            },
            error: function(){
                $('.result').append('Invalid Credentials');
            }
        });
        return false;
    },
    logout: function(){
        localStorage.clear();
        window.location = '/login';
    },
    me: localStorage.getItem("user"),
    roles: function(){
        return my("roles");
    },
    permissions: function(){
        return my('permissions');
    },
    redirectOnLeads: function(){
        window.location.replace("http://"+window.location.hostname+'/leads/my/?token='+this.token);
    }
};



/************ APP ***********/
var App = {
    prepare: function(){
        if(Auth.check){
            $('.login').hide();
        }else{
            $('.login').show();
        }
    },
    start: function(){}
};

function showLoader(){
    $('#loader').show();
}
function hideLoader(){
    $('#loader').hide();
}

function my(item){
    return JSON.parse(localStorage.getItem(item))
}