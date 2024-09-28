"use strict";const t=require("../common/vendor.js");exports.useCopy=function(){return{copy:async a=>{try{await t.index.setClipboardData({data:String(a)})}catch(e){t.index.$u.toast(e)}}}};
