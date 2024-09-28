"use strict";const i=require("../isNumeric/index.js"),e=require("../isDef/index.js");exports.addUnit=function(r){if(e.isDef(r))return r=String(r),i.isNumeric(r)?`${r}px`:r};
