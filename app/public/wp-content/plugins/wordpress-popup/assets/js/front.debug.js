(function(t){var e=typeof self=="object"&&self.self===self&&self||typeof global=="object"&&global.global===global&&global;if(typeof define==="function"&&define.amd){define(["underscore","jquery","exports"],function(i,r,n){e.Backbone=t(e,n,i,r)})}else if(typeof exports!=="undefined"){var i=require("underscore"),r;try{r=require("jquery")}catch(n){}t(e,exports,i,r)}else{e.Backbone=t(e,{},e._,e.jQuery||e.Zepto||e.ender||e.$)}})(function(t,e,i,r){var n=t.Backbone;var s=Array.prototype.slice;e.VERSION="1.3.3";e.$=r;e.noConflict=function(){t.Backbone=n;return this};e.emulateHTTP=false;e.emulateJSON=false;var a=function(t,e,r){switch(t){case 1:return function(){return i[e](this[r])};case 2:return function(t){return i[e](this[r],t)};case 3:return function(t,n){return i[e](this[r],o(t,this),n)};case 4:return function(t,n,s){return i[e](this[r],o(t,this),n,s)};default:return function(){var t=s.call(arguments);t.unshift(this[r]);return i[e].apply(i,t)}}};var h=function(t,e,r){i.each(e,function(e,n){if(i[n])t.prototype[n]=a(e,n,r)})};var o=function(t,e){if(i.isFunction(t))return t;if(i.isObject(t)&&!e._isModel(t))return l(t);if(i.isString(t))return function(e){return e.get(t)};return t};var l=function(t){var e=i.matches(t);return function(t){return e(t.attributes)}};var u=e.Events={};var c=/\s+/;var f=function(t,e,r,n,s){var a=0,h;if(r&&typeof r==="object"){if(n!==void 0&&"context"in s&&s.context===void 0)s.context=n;for(h=i.keys(r);a<h.length;a++){e=f(t,e,h[a],r[h[a]],s)}}else if(r&&c.test(r)){for(h=r.split(c);a<h.length;a++){e=t(e,h[a],n,s)}}else{e=t(e,r,n,s)}return e};u.on=function(t,e,i){return d(this,t,e,i)};var d=function(t,e,i,r,n){t._events=f(v,t._events||{},e,i,{context:r,ctx:t,listening:n});if(n){var s=t._listeners||(t._listeners={});s[n.id]=n}return t};u.listenTo=function(t,e,r){if(!t)return this;var n=t._listenId||(t._listenId=i.uniqueId("l"));var s=this._listeningTo||(this._listeningTo={});var a=s[n];if(!a){var h=this._listenId||(this._listenId=i.uniqueId("l"));a=s[n]={obj:t,objId:n,id:h,listeningTo:s,count:0}}d(t,e,r,this,a);return this};var v=function(t,e,i,r){if(i){var n=t[e]||(t[e]=[]);var s=r.context,a=r.ctx,h=r.listening;if(h)h.count++;n.push({callback:i,context:s,ctx:s||a,listening:h})}return t};u.off=function(t,e,i){if(!this._events)return this;this._events=f(g,this._events,t,e,{context:i,listeners:this._listeners});return this};u.stopListening=function(t,e,r){var n=this._listeningTo;if(!n)return this;var s=t?[t._listenId]:i.keys(n);for(var a=0;a<s.length;a++){var h=n[s[a]];if(!h)break;h.obj.off(e,r,this)}return this};var g=function(t,e,r,n){if(!t)return;var s=0,a;var h=n.context,o=n.listeners;if(!e&&!r&&!h){var l=i.keys(o);for(;s<l.length;s++){a=o[l[s]];delete o[a.id];delete a.listeningTo[a.objId]}return}var u=e?[e]:i.keys(t);for(;s<u.length;s++){e=u[s];var c=t[e];if(!c)break;var f=[];for(var d=0;d<c.length;d++){var v=c[d];if(r&&r!==v.callback&&r!==v.callback._callback||h&&h!==v.context){f.push(v)}else{a=v.listening;if(a&&--a.count===0){delete o[a.id];delete a.listeningTo[a.objId]}}}if(f.length){t[e]=f}else{delete t[e]}}return t};u.once=function(t,e,r){var n=f(p,{},t,e,i.bind(this.off,this));if(typeof t==="string"&&r==null)e=void 0;return this.on(n,e,r)};u.listenToOnce=function(t,e,r){var n=f(p,{},e,r,i.bind(this.stopListening,this,t));return this.listenTo(t,n)};var p=function(t,e,r,n){if(r){var s=t[e]=i.once(function(){n(e,s);r.apply(this,arguments)});s._callback=r}return t};u.trigger=function(t){if(!this._events)return this;var e=Math.max(0,arguments.length-1);var i=Array(e);for(var r=0;r<e;r++)i[r]=arguments[r+1];f(m,this._events,t,void 0,i);return this};var m=function(t,e,i,r){if(t){var n=t[e];var s=t.all;if(n&&s)s=s.slice();if(n)_(n,r);if(s)_(s,[e].concat(r))}return t};var _=function(t,e){var i,r=-1,n=t.length,s=e[0],a=e[1],h=e[2];switch(e.length){case 0:while(++r<n)(i=t[r]).callback.call(i.ctx);return;case 1:while(++r<n)(i=t[r]).callback.call(i.ctx,s);return;case 2:while(++r<n)(i=t[r]).callback.call(i.ctx,s,a);return;case 3:while(++r<n)(i=t[r]).callback.call(i.ctx,s,a,h);return;default:while(++r<n)(i=t[r]).callback.apply(i.ctx,e);return}};u.bind=u.on;u.unbind=u.off;i.extend(e,u);var y=e.Model=function(t,e){var r=t||{};e||(e={});this.cid=i.uniqueId(this.cidPrefix);this.attributes={};if(e.collection)this.collection=e.collection;if(e.parse)r=this.parse(r,e)||{};var n=i.result(this,"defaults");r=i.defaults(i.extend({},n,r),n);this.set(r,e);this.changed={};this.initialize.apply(this,arguments)};i.extend(y.prototype,u,{changed:null,validationError:null,idAttribute:"id",cidPrefix:"c",initialize:function(){},toJSON:function(t){return i.clone(this.attributes)},sync:function(){return e.sync.apply(this,arguments)},get:function(t){return this.attributes[t]},escape:function(t){return i.escape(this.get(t))},has:function(t){return this.get(t)!=null},matches:function(t){return!!i.iteratee(t,this)(this.attributes)},set:function(t,e,r){if(t==null)return this;var n;if(typeof t==="object"){n=t;r=e}else{(n={})[t]=e}r||(r={});if(!this._validate(n,r))return false;var s=r.unset;var a=r.silent;var h=[];var o=this._changing;this._changing=true;if(!o){this._previousAttributes=i.clone(this.attributes);this.changed={}}var l=this.attributes;var u=this.changed;var c=this._previousAttributes;for(var f in n){e=n[f];if(!i.isEqual(l[f],e))h.push(f);if(!i.isEqual(c[f],e)){u[f]=e}else{delete u[f]}s?delete l[f]:l[f]=e}if(this.idAttribute in n)this.id=this.get(this.idAttribute);if(!a){if(h.length)this._pending=r;for(var d=0;d<h.length;d++){this.trigger("change:"+h[d],this,l[h[d]],r)}}if(o)return this;if(!a){while(this._pending){r=this._pending;this._pending=false;this.trigger("change",this,r)}}this._pending=false;this._changing=false;return this},unset:function(t,e){return this.set(t,void 0,i.extend({},e,{unset:true}))},clear:function(t){var e={};for(var r in this.attributes)e[r]=void 0;return this.set(e,i.extend({},t,{unset:true}))},hasChanged:function(t){if(t==null)return!i.isEmpty(this.changed);return i.has(this.changed,t)},changedAttributes:function(t){if(!t)return this.hasChanged()?i.clone(this.changed):false;var e=this._changing?this._previousAttributes:this.attributes;var r={};for(var n in t){var s=t[n];if(i.isEqual(e[n],s))continue;r[n]=s}return i.size(r)?r:false},previous:function(t){if(t==null||!this._previousAttributes)return null;return this._previousAttributes[t]},previousAttributes:function(){return i.clone(this._previousAttributes)},fetch:function(t){t=i.extend({parse:true},t);var e=this;var r=t.success;t.success=function(i){var n=t.parse?e.parse(i,t):i;if(!e.set(n,t))return false;if(r)r.call(t.context,e,i,t);e.trigger("sync",e,i,t)};B(this,t);return this.sync("read",this,t)},save:function(t,e,r){var n;if(t==null||typeof t==="object"){n=t;r=e}else{(n={})[t]=e}r=i.extend({validate:true,parse:true},r);var s=r.wait;if(n&&!s){if(!this.set(n,r))return false}else if(!this._validate(n,r)){return false}var a=this;var h=r.success;var o=this.attributes;r.success=function(t){a.attributes=o;var e=r.parse?a.parse(t,r):t;if(s)e=i.extend({},n,e);if(e&&!a.set(e,r))return false;if(h)h.call(r.context,a,t,r);a.trigger("sync",a,t,r)};B(this,r);if(n&&s)this.attributes=i.extend({},o,n);var l=this.isNew()?"create":r.patch?"patch":"update";if(l==="patch"&&!r.attrs)r.attrs=n;var u=this.sync(l,this,r);this.attributes=o;return u},destroy:function(t){t=t?i.clone(t):{};var e=this;var r=t.success;var n=t.wait;var s=function(){e.stopListening();e.trigger("destroy",e,e.collection,t)};t.success=function(i){if(n)s();if(r)r.call(t.context,e,i,t);if(!e.isNew())e.trigger("sync",e,i,t)};var a=false;if(this.isNew()){i.defer(t.success)}else{B(this,t);a=this.sync("delete",this,t)}if(!n)s();return a},url:function(){var t=i.result(this,"urlRoot")||i.result(this.collection,"url")||F();if(this.isNew())return t;var e=this.get(this.idAttribute);return t.replace(/[^\/]$/,"$&/")+encodeURIComponent(e)},parse:function(t,e){return t},clone:function(){return new this.constructor(this.attributes)},isNew:function(){return!this.has(this.idAttribute)},isValid:function(t){return this._validate({},i.extend({},t,{validate:true}))},_validate:function(t,e){if(!e.validate||!this.validate)return true;t=i.extend({},this.attributes,t);var r=this.validationError=this.validate(t,e)||null;if(!r)return true;this.trigger("invalid",this,r,i.extend(e,{validationError:r}));return false}});var b={keys:1,values:1,pairs:1,invert:1,pick:0,omit:0,chain:1,isEmpty:1};h(y,b,"attributes");var x=e.Collection=function(t,e){e||(e={});if(e.model)this.model=e.model;if(e.comparator!==void 0)this.comparator=e.comparator;this._reset();this.initialize.apply(this,arguments);if(t)this.reset(t,i.extend({silent:true},e))};var w={add:true,remove:true,merge:true};var E={add:true,remove:false};var I=function(t,e,i){i=Math.min(Math.max(i,0),t.length);var r=Array(t.length-i);var n=e.length;var s;for(s=0;s<r.length;s++)r[s]=t[s+i];for(s=0;s<n;s++)t[s+i]=e[s];for(s=0;s<r.length;s++)t[s+n+i]=r[s]};i.extend(x.prototype,u,{model:y,initialize:function(){},toJSON:function(t){return this.map(function(e){return e.toJSON(t)})},sync:function(){return e.sync.apply(this,arguments)},add:function(t,e){return this.set(t,i.extend({merge:false},e,E))},remove:function(t,e){e=i.extend({},e);var r=!i.isArray(t);t=r?[t]:t.slice();var n=this._removeModels(t,e);if(!e.silent&&n.length){e.changes={added:[],merged:[],removed:n};this.trigger("update",this,e)}return r?n[0]:n},set:function(t,e){if(t==null)return;e=i.extend({},w,e);if(e.parse&&!this._isModel(t)){t=this.parse(t,e)||[]}var r=!i.isArray(t);t=r?[t]:t.slice();var n=e.at;if(n!=null)n=+n;if(n>this.length)n=this.length;if(n<0)n+=this.length+1;var s=[];var a=[];var h=[];var o=[];var l={};var u=e.add;var c=e.merge;var f=e.remove;var d=false;var v=this.comparator&&n==null&&e.sort!==false;var g=i.isString(this.comparator)?this.comparator:null;var p,m;for(m=0;m<t.length;m++){p=t[m];var _=this.get(p);if(_){if(c&&p!==_){var y=this._isModel(p)?p.attributes:p;if(e.parse)y=_.parse(y,e);_.set(y,e);h.push(_);if(v&&!d)d=_.hasChanged(g)}if(!l[_.cid]){l[_.cid]=true;s.push(_)}t[m]=_}else if(u){p=t[m]=this._prepareModel(p,e);if(p){a.push(p);this._addReference(p,e);l[p.cid]=true;s.push(p)}}}if(f){for(m=0;m<this.length;m++){p=this.models[m];if(!l[p.cid])o.push(p)}if(o.length)this._removeModels(o,e)}var b=false;var x=!v&&u&&f;if(s.length&&x){b=this.length!==s.length||i.some(this.models,function(t,e){return t!==s[e]});this.models.length=0;I(this.models,s,0);this.length=this.models.length}else if(a.length){if(v)d=true;I(this.models,a,n==null?this.length:n);this.length=this.models.length}if(d)this.sort({silent:true});if(!e.silent){for(m=0;m<a.length;m++){if(n!=null)e.index=n+m;p=a[m];p.trigger("add",p,this,e)}if(d||b)this.trigger("sort",this,e);if(a.length||o.length||h.length){e.changes={added:a,removed:o,merged:h};this.trigger("update",this,e)}}return r?t[0]:t},reset:function(t,e){e=e?i.clone(e):{};for(var r=0;r<this.models.length;r++){this._removeReference(this.models[r],e)}e.previousModels=this.models;this._reset();t=this.add(t,i.extend({silent:true},e));if(!e.silent)this.trigger("reset",this,e);return t},push:function(t,e){return this.add(t,i.extend({at:this.length},e))},pop:function(t){var e=this.at(this.length-1);return this.remove(e,t)},unshift:function(t,e){return this.add(t,i.extend({at:0},e))},shift:function(t){var e=this.at(0);return this.remove(e,t)},slice:function(){return s.apply(this.models,arguments)},get:function(t){if(t==null)return void 0;return this._byId[t]||this._byId[this.modelId(t.attributes||t)]||t.cid&&this._byId[t.cid]},has:function(t){return this.get(t)!=null},at:function(t){if(t<0)t+=this.length;return this.models[t]},where:function(t,e){return this[e?"find":"filter"](t)},findWhere:function(t){return this.where(t,true)},sort:function(t){var e=this.comparator;if(!e)throw new Error("Cannot sort a set without a comparator");t||(t={});var r=e.length;if(i.isFunction(e))e=i.bind(e,this);if(r===1||i.isString(e)){this.models=this.sortBy(e)}else{this.models.sort(e)}if(!t.silent)this.trigger("sort",this,t);return this},pluck:function(t){return this.map(t+"")},fetch:function(t){t=i.extend({parse:true},t);var e=t.success;var r=this;t.success=function(i){var n=t.reset?"reset":"set";r[n](i,t);if(e)e.call(t.context,r,i,t);r.trigger("sync",r,i,t)};B(this,t);return this.sync("read",this,t)},create:function(t,e){e=e?i.clone(e):{};var r=e.wait;t=this._prepareModel(t,e);if(!t)return false;if(!r)this.add(t,e);var n=this;var s=e.success;e.success=function(t,e,i){if(r)n.add(t,i);if(s)s.call(i.context,t,e,i)};t.save(null,e);return t},parse:function(t,e){return t},clone:function(){return new this.constructor(this.models,{model:this.model,comparator:this.comparator})},modelId:function(t){return t[this.model.prototype.idAttribute||"id"]},_reset:function(){this.length=0;this.models=[];this._byId={}},_prepareModel:function(t,e){if(this._isModel(t)){if(!t.collection)t.collection=this;return t}e=e?i.clone(e):{};e.collection=this;var r=new this.model(t,e);if(!r.validationError)return r;this.trigger("invalid",this,r.validationError,e);return false},_removeModels:function(t,e){var i=[];for(var r=0;r<t.length;r++){var n=this.get(t[r]);if(!n)continue;var s=this.indexOf(n);this.models.splice(s,1);this.length--;delete this._byId[n.cid];var a=this.modelId(n.attributes);if(a!=null)delete this._byId[a];if(!e.silent){e.index=s;n.trigger("remove",n,this,e)}i.push(n);this._removeReference(n,e)}return i},_isModel:function(t){return t instanceof y},_addReference:function(t,e){this._byId[t.cid]=t;var i=this.modelId(t.attributes);if(i!=null)this._byId[i]=t;t.on("all",this._onModelEvent,this)},_removeReference:function(t,e){delete this._byId[t.cid];var i=this.modelId(t.attributes);if(i!=null)delete this._byId[i];if(this===t.collection)delete t.collection;t.off("all",this._onModelEvent,this)},_onModelEvent:function(t,e,i,r){if(e){if((t==="add"||t==="remove")&&i!==this)return;if(t==="destroy")this.remove(e,r);if(t==="change"){var n=this.modelId(e.previousAttributes());var s=this.modelId(e.attributes);if(n!==s){if(n!=null)delete this._byId[n];if(s!=null)this._byId[s]=e}}}this.trigger.apply(this,arguments)}});var S={forEach:3,each:3,map:3,collect:3,reduce:0,foldl:0,inject:0,reduceRight:0,foldr:0,find:3,detect:3,filter:3,select:3,reject:3,every:3,all:3,some:3,any:3,include:3,includes:3,contains:3,invoke:0,max:3,min:3,toArray:1,size:1,first:3,head:3,take:3,initial:3,rest:3,tail:3,drop:3,last:3,without:0,difference:0,indexOf:3,shuffle:1,lastIndexOf:3,isEmpty:1,chain:1,sample:3,partition:3,groupBy:3,countBy:3,sortBy:3,indexBy:3,findIndex:3,findLastIndex:3};h(x,S,"models");var k=e.View=function(t){this.cid=i.uniqueId("view");i.extend(this,i.pick(t,P));this._ensureElement();this.initialize.apply(this,arguments)};var T=/^(\S+)\s*(.*)$/;var P=["model","collection","el","id","attributes","className","tagName","events"];i.extend(k.prototype,u,{tagName:"div",$:function(t){return this.$el.find(t)},initialize:function(){},render:function(){return this},remove:function(){this._removeElement();this.stopListening();return this},_removeElement:function(){this.$el.remove()},setElement:function(t){this.undelegateEvents();this._setElement(t);this.delegateEvents();return this},_setElement:function(t){this.$el=t instanceof e.$?t:e.$(t);this.el=this.$el[0]},delegateEvents:function(t){t||(t=i.result(this,"events"));if(!t)return this;this.undelegateEvents();for(var e in t){var r=t[e];if(!i.isFunction(r))r=this[r];if(!r)continue;var n=e.match(T);this.delegate(n[1],n[2],i.bind(r,this))}return this},delegate:function(t,e,i){this.$el.on(t+".delegateEvents"+this.cid,e,i);return this},undelegateEvents:function(){if(this.$el)this.$el.off(".delegateEvents"+this.cid);return this},undelegate:function(t,e,i){this.$el.off(t+".delegateEvents"+this.cid,e,i);return this},_createElement:function(t){return document.createElement(t)},_ensureElement:function(){if(!this.el){var t=i.extend({},i.result(this,"attributes"));if(this.id)t.id=i.result(this,"id");if(this.className)t["class"]=i.result(this,"className");this.setElement(this._createElement(i.result(this,"tagName")));this._setAttributes(t)}else{this.setElement(i.result(this,"el"))}},_setAttributes:function(t){this.$el.attr(t)}});e.sync=function(t,r,n){var s=H[t];i.defaults(n||(n={}),{emulateHTTP:e.emulateHTTP,emulateJSON:e.emulateJSON});var a={type:s,dataType:"json"};if(!n.url){a.url=i.result(r,"url")||F()}if(n.data==null&&r&&(t==="create"||t==="update"||t==="patch")){a.contentType="application/json";a.data=JSON.stringify(n.attrs||r.toJSON(n))}if(n.emulateJSON){a.contentType="application/x-www-form-urlencoded";a.data=a.data?{model:a.data}:{}}if(n.emulateHTTP&&(s==="PUT"||s==="DELETE"||s==="PATCH")){a.type="POST";if(n.emulateJSON)a.data._method=s;var h=n.beforeSend;n.beforeSend=function(t){t.setRequestHeader("X-HTTP-Method-Override",s);if(h)return h.apply(this,arguments)}}if(a.type!=="GET"&&!n.emulateJSON){a.processData=false}var o=n.error;n.error=function(t,e,i){n.textStatus=e;n.errorThrown=i;if(o)o.call(n.context,t,e,i)};var l=n.xhr=e.ajax(i.extend(a,n));r.trigger("request",r,l,n);return l};var H={create:"POST",update:"PUT",patch:"PATCH","delete":"DELETE",read:"GET"};e.ajax=function(){return e.$.ajax.apply(e.$,arguments)};var $=e.Router=function(t){t||(t={});if(t.routes)this.routes=t.routes;this._bindRoutes();this.initialize.apply(this,arguments)};var A=/\((.*?)\)/g;var C=/(\(\?)?:\w+/g;var R=/\*\w+/g;var j=/[\-{}\[\]+?.,\\\^$|#\s]/g;i.extend($.prototype,u,{initialize:function(){},route:function(t,r,n){if(!i.isRegExp(t))t=this._routeToRegExp(t);if(i.isFunction(r)){n=r;r=""}if(!n)n=this[r];var s=this;e.history.route(t,function(i){var a=s._extractParameters(t,i);if(s.execute(n,a,r)!==false){s.trigger.apply(s,["route:"+r].concat(a));s.trigger("route",r,a);e.history.trigger("route",s,r,a)}});return this},execute:function(t,e,i){if(t)t.apply(this,e)},navigate:function(t,i){e.history.navigate(t,i);return this},_bindRoutes:function(){if(!this.routes)return;this.routes=i.result(this,"routes");var t,e=i.keys(this.routes);while((t=e.pop())!=null){this.route(t,this.routes[t])}},_routeToRegExp:function(t){t=t.replace(j,"\\$&").replace(A,"(?:$1)?").replace(C,function(t,e){return e?t:"([^/?]+)"}).replace(R,"([^?]*?)");return new RegExp("^"+t+"(?:\\?([\\s\\S]*))?$")},_extractParameters:function(t,e){var r=t.exec(e).slice(1);return i.map(r,function(t,e){if(e===r.length-1)return t||null;return t?decodeURIComponent(t):null})}});var N=e.History=function(){this.handlers=[];this.checkUrl=i.bind(this.checkUrl,this);if(typeof window!=="undefined"){this.location=window.location;this.history=window.history}};var M=/^[#\/]|\s+$/g;var O=/^\/+|\/+$/g;var U=/#.*$/;N.started=false;i.extend(N.prototype,u,{interval:50,atRoot:function(){var t=this.location.pathname.replace(/[^\/]$/,"$&/");return t===this.root&&!this.getSearch()},matchRoot:function(){var t=this.decodeFragment(this.location.pathname);var e=t.slice(0,this.root.length-1)+"/";return e===this.root},decodeFragment:function(t){return decodeURI(t.replace(/%25/g,"%2525"))},getSearch:function(){var t=this.location.href.replace(/#.*/,"").match(/\?.+/);return t?t[0]:""},getHash:function(t){var e=(t||this).location.href.match(/#(.*)$/);return e?e[1]:""},getPath:function(){var t=this.decodeFragment(this.location.pathname+this.getSearch()).slice(this.root.length-1);return t.charAt(0)==="/"?t.slice(1):t},getFragment:function(t){if(t==null){if(this._usePushState||!this._wantsHashChange){t=this.getPath()}else{t=this.getHash()}}return t.replace(M,"")},start:function(t){if(N.started)throw new Error("Backbone.history has already been started");N.started=true;this.options=i.extend({root:"/"},this.options,t);this.root=this.options.root;this._wantsHashChange=this.options.hashChange!==false;this._hasHashChange="onhashchange"in window&&(document.documentMode===void 0||document.documentMode>7);this._useHashChange=this._wantsHashChange&&this._hasHashChange;this._wantsPushState=!!this.options.pushState;this._hasPushState=!!(this.history&&this.history.pushState);this._usePushState=this._wantsPushState&&this._hasPushState;this.fragment=this.getFragment();this.root=("/"+this.root+"/").replace(O,"/");if(this._wantsHashChange&&this._wantsPushState){if(!this._hasPushState&&!this.atRoot()){var e=this.root.slice(0,-1)||"/";this.location.replace(e+"#"+this.getPath());return true}else if(this._hasPushState&&this.atRoot()){this.navigate(this.getHash(),{replace:true})}}if(!this._hasHashChange&&this._wantsHashChange&&!this._usePushState){this.iframe=document.createElement("iframe");this.iframe.src="javascript:0";this.iframe.style.display="none";this.iframe.tabIndex=-1;var r=document.body;var n=r.insertBefore(this.iframe,r.firstChild).contentWindow;n.document.open();n.document.close();n.location.hash="#"+this.fragment}var s=window.addEventListener||function(t,e){return attachEvent("on"+t,e)};if(this._usePushState){s("popstate",this.checkUrl,false)}else if(this._useHashChange&&!this.iframe){s("hashchange",this.checkUrl,false)}else if(this._wantsHashChange){this._checkUrlInterval=setInterval(this.checkUrl,this.interval)}if(!this.options.silent)return this.loadUrl()},stop:function(){var t=window.removeEventListener||function(t,e){return detachEvent("on"+t,e)};if(this._usePushState){t("popstate",this.checkUrl,false)}else if(this._useHashChange&&!this.iframe){t("hashchange",this.checkUrl,false)}if(this.iframe){document.body.removeChild(this.iframe);this.iframe=null}if(this._checkUrlInterval)clearInterval(this._checkUrlInterval);N.started=false},route:function(t,e){this.handlers.unshift({route:t,callback:e})},checkUrl:function(t){var e=this.getFragment();if(e===this.fragment&&this.iframe){e=this.getHash(this.iframe.contentWindow)}if(e===this.fragment)return false;if(this.iframe)this.navigate(e);this.loadUrl()},loadUrl:function(t){if(!this.matchRoot())return false;t=this.fragment=this.getFragment(t);return i.some(this.handlers,function(e){if(e.route.test(t)){e.callback(t);return true}})},navigate:function(t,e){if(!N.started)return false;if(!e||e===true)e={trigger:!!e};t=this.getFragment(t||"");var i=this.root;if(t===""||t.charAt(0)==="?"){i=i.slice(0,-1)||"/"}var r=i+t;t=this.decodeFragment(t.replace(U,""));if(this.fragment===t)return;this.fragment=t;if(this._usePushState){this.history[e.replace?"replaceState":"pushState"]({},document.title,r)}else if(this._wantsHashChange){this._updateHash(this.location,t,e.replace);if(this.iframe&&t!==this.getHash(this.iframe.contentWindow)){var n=this.iframe.contentWindow;if(!e.replace){n.document.open();n.document.close()}this._updateHash(n.location,t,e.replace)}}else{return this.location.assign(r)}if(e.trigger)return this.loadUrl(t)},_updateHash:function(t,e,i){if(i){var r=t.href.replace(/(javascript:|#).*$/,"");t.replace(r+"#"+e)}else{t.hash="#"+e}}});e.history=new N;var q=function(t,e){var r=this;var n;if(t&&i.has(t,"constructor")){n=t.constructor}else{n=function(){return r.apply(this,arguments)}}i.extend(n,r,e);n.prototype=i.create(r.prototype,t);n.prototype.constructor=n;n.__super__=r.prototype;return n};y.extend=x.extend=$.extend=k.extend=N.extend=q;var F=function(){throw new Error('A "url" property or function must be specified')};var B=function(t,e){var i=e.error;e.error=function(r){if(i)i.call(e.context,t,r,e);t.trigger("error",t,r,e)}};return e});
( function( $ ) {
	'use strict';

	/**
	 * Defines the Hustle Object
	 *
	 * @type {{define, getModules, get, modules}}
	 */
	window.Hustle = ( function( $, doc, win ) {
		var _modules = {},
			_TemplateOptions = {
				evaluate: /<#([\s\S]+?)#>/g,
				interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
				escape: /\{\{([^\}]+?)\}\}(?!\})/g
			};

			let define = function( moduleName, module ) {
				var splits = moduleName.split( '.' );
				if ( splits.length ) { // if module_name has more than one object name, then add the module definition recursively
					let recursive = function( moduleName, modules ) {
						var arr = moduleName.split( '.' ),
							_moduleName = arr.splice( 0, 1 )[ 0 ],
							invoked;
						if ( ! _moduleName ) {
							return;
						}
						if ( ! arr.length ) {
							invoked = module.call( null, $, doc, win );
							modules[ _moduleName ] = _.isFunction( invoked ) ||
								'undefined' === typeof invoked ?
								invoked : _.extend( modules[ _moduleName ] || {}, invoked );
						} else {
							modules[ _moduleName ] = modules[ _moduleName ] || {};
						}
						if ( arr.length && _moduleName ) {
							recursive( arr.join( '.' ), modules[ _moduleName ]);
						}
					};
					recursive( moduleName, _modules );
				} else {
					let m = _modules[moduleName] || {};
					_modules[moduleName] = _.extend( m, module.call( null, $, doc, win ) );
				}
			},
			getModules = function() {
				return _modules;
			},
			get = function( moduleName ) {
				var module, recursive;
				if ( moduleName.split( '.' ).length ) { // recursively fetch the module
					module = false;
					recursive = function( moduleName, modules ) {
							var arr = moduleName.split( '.' ),
								_moduleName = arr.splice( 0, 1 )[ 0 ];
							module = modules[ _moduleName ];
							if ( arr.length ) {
								recursive( arr.join( '.' ), modules[ _moduleName ]);
							}
						};
					recursive( moduleName, _modules );
					return module;
				}
				return _modules[moduleName] || false;
			},
			Events = _.extend({}, Backbone.Events ),
			View = Backbone.View.extend({
				initialize: function() {
					if ( _.isFunction( this.initMix ) ) {
						this.initMix.apply( this, arguments );
					}
					if ( this.render ) {
						this.render = _.wrap( this.render, function( render ) {
							this.trigger( 'before_render' );
							render.call( this );
							Events.trigger( 'view.rendered', this );
							this.trigger( 'rendered' );
						});
					}
					if ( _.isFunction( this.init ) ) {
						this.init.apply( this, arguments );
					}
				}
			}),
			template = _.memoize( function( id ) {
				var compiled;
				return function( data ) {
					compiled = compiled || _.template( document.getElementById( id ).innerHTML, null, _TemplateOptions );
					return compiled( data ).replace( '/*<![CDATA[*/', '' ).replace( '/*]]>*/', '' );
				};
			}),
			createTemplate = _.memoize( function( str ) {
				var cache;
				return function( data ) {
					cache = cache || _.template( str, null, _TemplateOptions );
					return cache( data );
				};
			}),
			getTemplateOptions = function() {
				return $.extend(  true, {}, _TemplateOptions );
			},
			cookie = ( function() {

				// Get a cookie value.
				var get = function( name ) {
					var i, c, cookieName, value,
						ca = document.cookie.split( ';' ),
						caLength = ca.length;
					cookieName = name + '=';
					for ( i = 0; i < caLength; i += 1 ) {
						c = ca[i];
						while ( ' ' === c.charAt( 0 ) ) {
							c = c.substring( 1, c.length );
						}
						if ( 0 === c.indexOf( cookieName ) ) {
							let _val = c.substring( cookieName.length, c.length );
							return _val ? JSON.parse( _val ) : _val;
						}
					}
					return null;
				};

				// Saves the value into a cookie.
				var set = function( name, value, days ) {
					var date, expires;

					value = $.isArray( value ) || $.isPlainObject( value ) ? JSON.stringify( value ) : value;
					if ( ! isNaN( days ) ) {
						date = new Date();
						date.setTime( date.getTime() + ( days * 24 * 60 * 60 * 1000 ) );
						expires = '; expires=' + date.toGMTString();
					} else {
						expires = '';
					}
					document.cookie = name + '=' + value + expires + '; path=/';
				};
				return {
					set: set,
					get: get
				};
			}() ),
			consts = ( function() {
				return {
					ModuleShowCount: 'hustle_module_show_count-'
				};
			}() );

		return {
			define,
			getModules,
			get,
			Events,
			View,
			template,
			createTemplate,
			getTemplateOptions,
			cookie,
			consts
		};
	}( jQuery, document, window ) );

}( jQuery ) );

var  Optin = Optin || {};

Optin.View = {};
Optin.Models = {};
Optin.Events = {};

if ( 'undefined' !== typeof Backbone ) {
	_.extend( Optin.Events, Backbone.Events );
}

( function( $ ) {
	'use strict';
	Optin.NEVER_SEE_PREFIX = 'inc_optin_never_see_again-',
	Optin.COOKIE_PREFIX = 'inc_optin_long_hidden-';
	Optin.POPUP_COOKIE_PREFIX = 'inc_optin_popup_long_hidden-';
	Optin.SLIDE_IN_COOKIE_PREFIX = 'inc_optin_slide_in_long_hidden-';
	Optin.EMBEDDED_COOKIE_PREFIX = 'inc_optin_embedded_long_hidden-';

	Optin.globalMixin = function() {
		_.mixin({

			/**
			 * Logs to console
			 */
			log: function() {
				console.log( arguments );
			},

			/**
			 * Converts val to boolian
			 *
			 * @param val
			 * @returns {*}
			 */
			toBool: function( val ) {
				if ( _.isBoolean( val ) ) {
					return val;
				}
				if ( _.isString( val ) && -1 !== [ 'true', 'false', '1' ].indexOf( val.toLowerCase() ) ) {
					return 'true' === val.toLowerCase() || '1' === val.toLowerCase() ? true : false;
				}
				if ( _.isNumber( val ) ) {
					return ! ! val;
				}
				if ( _.isUndefined( val ) || _.isNull( val ) || _.isNaN( val ) ) {
					return false;
				}
				return val;
			},

			/**
			 * Checks if val is truthy
			 *
			 * @param val
			 * @returns {boolean}
			 */
			isTrue: function( val ) {
				if ( _.isUndefined( val ) || _.isNull( val ) || _.isNaN( val ) ) {
					return false;
				}
				if ( _.isNumber( val ) ) {
					return 0 !== val;
				}
				val = val.toString().toLowerCase();
				return -1 !== [ '1', 'true', 'on' ].indexOf( val );
			},
			isFalse: function( val ) {
				return ! _.isTrue( val );
			},
			controlBase: function( checked, current, attribute ) {
				attribute = _.isUndefined( attribute ) ? 'checked' : attribute;
				checked  = _.toBool( checked );
				current = _.isBoolean( checked ) ? _.isTrue( current ) : current;
				if ( _.isEqual( checked, current ) ) {
					return  attribute + '=' + attribute;
				}
				return '';
			},

			/**
			 * Returns checked=check if checked variable is equal to current state
			 *
			 *
			 * @param checked checked state
			 * @param current current state
			 * @returns {*}
			 */
			checked: function( checked, current ) {
				return _.controlBase( checked, current, 'checked' );
			},

			/**
			 * Adds selected attribute
			 *
			 * @param selected
			 * @param current
			 * @returns {*}
			 */
			selected: function( selected, current ) {
				return _.controlBase( selected, current, 'selected' );
			},

			/**
			 * Adds disabled attribute
			 *
			 * @param disabled
			 * @param current
			 * @returns {*}
			 */
			disabled: function( disabled, current ) {
				return _.controlBase( disabled, current, 'disabled' );
			},

			/**
			 * Returns css class based on the passed in condition
			 *
			 * @param conditon
			 * @param cls
			 * @param negating_cls
			 * @returns {*}
			 */
			class: function( conditon, cls, negatingCls ) {
				if ( _.isTrue( conditon ) ) {
					return cls;
				}
				return 'undefined' !== typeof negatingCls ? negatingCls : '';
			},

			/**
			 * Returns class attribute with relevant class name
			 *
			 * @param conditon
			 * @param cls
			 * @param negating_cls
			 * @returns {string}
			 */
			add_class: function( conditon, cls, negatingCls ) { // eslint-disable-line camelcase
				return 'class={class}'.replace( '{class}',  _.class( conditon, cls, negatingCls ) );
			},

			toUpperCase: function( str ) {
				return  _.isString( str ) ? str.toUpperCase() : '';
			}
		});

		if ( ! _.findKey ) {
			_.mixin({
				findKey: function( obj, predicate, context ) {
					predicate = cb( predicate, context );
					let keys = _.keys( obj ),
                        key;
					for ( let i = 0, length = keys.length; i < length; i++ ) {
						key = keys[i];
						if ( predicate( obj[ key ], key, obj ) ) {
							return key;
						}
					}
				}
			});
		}
	};

	Optin.globalMixin();

	/**
	 * Recursive toJSON
	 *
	 * @returns {*}
	 */
	Backbone.Model.prototype.toJSON = function() {
		var json = _.clone( this.attributes );
		var attr;
		for ( attr in json ) {
			if (
				( json[ attr ] instanceof Backbone.Model ) ||
				( Backbone.Collection && json[attr] instanceof Backbone.Collection )
			) {
				json[ attr ] = json[ attr ].toJSON();
			}
		}
		return json;
	};

	Optin.template = _.memoize( function( id ) {
		var compiled,

			options = {
				evaluate: /<#([\s\S]+?)#>/g,
				interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
				escape: /\{\{([^\}]+?)\}\}(?!\})/g
			};

		return function( data ) {
			compiled = compiled || _.template( $( '#' + id ).html(), null, options );
			return compiled( data ).replace( '/*<![CDATA[*/', '' ).replace( '/*]]>*/', '' );
		};
	});

	/**
	 * Compatibility with other plugin/theme e.g. upfront
	 *
	 */
	Optin.templateCompat = _.memoize( function( id ) {
		var compiled;

		return function( data ) {
			compiled = compiled || _.template( $( '#' + id ).html() );
			return compiled( data ).replace( '/*<![CDATA[*/', '' ).replace( '/*]]>*/', '' );
		};
	});

	Optin.cookie = Hustle.cookie;

	Optin.Mixins = {
		_mixins: {},
		_servicesMixins: {},
		_desingMixins: {},
		_displayMixins: {},
		add: function( id, obj ) {
			this._mixins[id] = obj;
		},
		getMixins: function() {
			return this._mixins;
		},
		addServicesMixin: function( id, obj ) {
			this._servicesMixins[id] = obj;
		},
		getServicesMixins: function() {
			return this._servicesMixins;
		}
	};


}( jQuery ) );

// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;// noinspection JSUnusedLocalSymbols
( function( $ ) {
	'use strict';

	var Optin = window.Optin || {};

	if ( incOpt.is_admin ) {
		return;
	}

	Optin.moduleLogView = Backbone.Model.extend({
		url: incOpt.ajaxurl + '?action=hustle_module_viewed',
		defaults: {
			'page_id': incOpt.page_id
		},
		parse: function( res ) {
			if ( res.success ) {
				console.log( 'Log success!' );
			} else {
				console.log( 'Log failed!' );
			}
		}
	});

	/**
	 * Log module view when it's being viewed
	 */
	$( document ).on( 'hustle:module:displayed', function( e, module ) {

		if ( 'object' === typeof module ) {

			const type = module.moduleType;

			// set cookies used for "show less than" display condition
			let showCountKey = Hustle.consts.ModuleShowCount + type + '-' + module.moduleId,
				currentShowCount = Hustle.cookie.get( showCountKey );
				Hustle.cookie.set( showCountKey, currentShowCount + 1, 30 );

			// Log number of times this module type has been shown so far
			const logType = 'undefined' !== module.$el.data( 'sub-type' ) ? module.$el.data( 'sub-type' ) : null;

			// TODO: check tracking types for embeds.
			if ( 'undefined' !== typeof Optin.moduleLogView && module.isTrackingEnabled ) {
				let logView = new Optin.moduleLogView();

				logView.set( 'module_sub_type', logType );
				logView.set( 'module_type', type );
				logView.set( 'module_id', module.moduleId );
				logView.save();
			}
		}
	});

	Optin.updateSshareNetworks = function( networks ) {

		const networksToRetrieve = 'undefined' === typeof networks ? Optin.networksToRetrieve : networks;

		if ( 'undefined' === typeof networksToRetrieve || ! networksToRetrieve.length ) {
			return;
		}

		// Retrieve the counters via ajax.
		$.ajax({
			type: 'POST',
			url: incOpt.ajaxurl,
			dataType: 'json',
			data: {
				action: 'hustle_update_network_shares',
				postId: incOpt.page_id,
				networks: networksToRetrieve
			}
		})
		.done( function( res ) {

			if ( res.success ) {

				const response = res.data;
				$.each( response.networks, function( network, counter ) {

					const $containers = $( `.hustle-share-icon[data-counter="native"][data-network="${network}"]` );

					if ( $containers.length ) {

						$containers.each( function() {
							const $counter = $( this ).find( '.hustle-counter' ),
								defaultCounter = parseInt( $( this ).data( 'count' ), 10 );

							if ( defaultCounter > parseInt( counter, 10 ) ) {
								counter = parseInt( defaultCounter, 10 );
							}

							let formatted = '';

							if ( 1000 > counter ) {
								formatted = counter;
							} else if ( 1000000 > counter ) {
								formatted = ( counter / 1000 ).toFixed( 1 ) + response.shorten.thousand;
							} else {
								formatted = ( counter / 1000000 ).toFixed( 1 ) + response.shorten.million;
							}
							$counter.text( formatted );

						});
					}
				});
			}
		});

	};

}( jQuery ) );

// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;// noinspection JSUnusedLocalSymbols
( function( $ ) {

	$( document ).on( 'submit', 'form.hustle-unsubscribe-form', function( e ) {
		e.preventDefault();

		let $form = $( e.target ),
			$emailHolder = $form.find( '.hustle-email-section' ),
			data = $form.serialize(),
			$button = $form.find( 'button' ),
			$failure = $form.find( '.wpoi-submit-failure' );

		$button.attr( 'disabled', true );
		$button.addClass( 'sui-button-onload' );

		$form.on( 'keypress', () => $failure.hide() );

		$.ajax({
			type: 'POST',
			url: incOpt.ajaxurl,
			dataType: 'json',
			data: {
				action: 'hustle_unsubscribe_form_submission',
				data: data
			},
			success( res ) {
				if ( res.success && true === res.success ) {
					$emailHolder.hide();
					$failure.hide();
					if ( res.data.wrapper && res.data.html ) {
						$form.find( res.data.wrapper ).html( res.data.html );
					}
				} else if ( res.data.html ) {
					$failure.text( res.data.html );
					$failure.show();
				}
			},
			error() {
				$failure.text( $failure.data( 'default-error' ) );
				$failure.show();
			},
			complete() {
				$button.attr( 'disabled', false );
				$button.removeClass( 'sui-button-onload' );
			}
		});
		return false;
	});

}( jQuery ) );

// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;// noinspection JSUnusedLocalSymbols
( function( $, doc, win ) {

	'use strict';

	if ( incOpt.is_upfront ) {
		return;
	}

	if ( ! incOpt.is_admin ) {
		Optin.ModuleLogConversion = Backbone.Model.extend({
			url: incOpt.ajaxurl + '?action=hustle_module_converted',
			defaults: {
				'page_id': incOpt.page_id
			},
			parse: function( res ) {
				if ( res.success ) {
					console.log( 'Log success!' );
				} else {
					console.log( 'Log failed!' );
				}
			}
		});
	}

	/**
	 * Front View Model
	 **/
	Optin.Module = Backbone.View.extend({
		moduleId: '',
		moduleType: '',
		settings: {},
		data: {},
		appearAfter: 'time',
		parent: 'body',
		cookieKey: '',
		neverSeeCookieKey: '',
		isShown: false,
		events: {
			'click .hustle-button-cta': 'ctaClicked'
		},
		close: _.noop,

		initialize: function( opts ) {
			this.data = opts;
			this.moduleId = opts.module_id;
			this.settings = opts.settings;

			this.moduleType = opts.module_type;

			this.isTrackingEnabled = 'enabled' === this.$el.data( 'tracking' );

			this.setOnInit( opts );

			if ( ! this.shouldDisplay() ) {
				this.$el.css( 'display', 'none' );
				return;
			}

			this.render();
		},

		setOnInit() {

			// Listen to successful Hustle's submission for optin to set cookies if needed.
			if ( 'optin' === this.data.module_mode ) {
				this.$el.off( 'submit' ).on( 'submit', $.proxy( this, 'handleSubmission' ) );
				this.$el.find( '.hustle-layout-form' ).off( 'change' ).on( 'change', $.proxy( this, 'onInputChange' ) );
			}

			// Calculate expiration days depends on what's been set
			this.expiration = parseInt( this.settings.expiration, 10 );
			this.expirationDays = this.getExpirationDays();
			this.appearAfter = this.settings.triggers.trigger;

			this.triggers = {

				onTimeDelay: this.settings.triggers.on_time_delay,
				onTimeUnit: this.settings.triggers.on_time_unit,
				onScroll: this.settings.triggers.on_scroll,
				onScrollPagePercent: parseInt( this.settings.triggers.on_scroll_page_percent ),
				onScrollCssSelector: this.settings.triggers.on_scroll_css_selector,
				enableOnClickElement: this.settings.triggers.enable_on_click_element,
				onClickElement: this.settings.triggers.on_click_element,
				enableOnClickShortcode: this.settings.triggers.enable_on_click_shortcode,
				onExitIntentperSession: this.settings.triggers.on_exit_intent_per_session,
				onExitIntentDelayed: this.settings.triggers.on_exit_intent_delayed,
				onExitIntentDelayedTime: this.settings.triggers.on_exit_intent_delayed_time,
				onExitIntentDelayedUnit: this.settings.triggers.on_exit_intent_delayed_unit,
				onAdblock: this.settings.triggers.on_adblock
			};

			if ( 'popup' === this.moduleType ) {
				this.cookieKey = Optin.POPUP_COOKIE_PREFIX + this.moduleId;
			} else if ( 'slidein' === this.moduleType ) {
				this.cookieKey = Optin.SLIDE_IN_COOKIE_PREFIX + this.moduleId;
			} else if ( 'embedded' === this.moduleType ) {
				this.cookieKey = Optin.EMBEDDED_COOKIE_PREFIX + this.moduleId;
			}
			this.neverSeeCookieKey = Optin.NEVER_SEE_PREFIX + this.moduleType + '-' + this.moduleId;

		},

		// Check if module should display.
		shouldDisplay: function() {
			if ( ! this.$el.length ) {
				return false;
			}

			let display,
				neverSee = Optin.cookie.get( this.neverSeeCookieKey );

			neverSee = parseInt( neverSee ) === parseInt( this.moduleId );
			if ( neverSee ) {
				display = false;
				return display;
			}
			if ( 'no_show_on_post' === this.settings.after_close ) {
				if ( 0 < parseInt( incOpt.page_id, 10 ) ) {
					display = ! _.isTrue( Optin.cookie.get( this.cookieKey + '_' + incOpt.page_id ) );
				} else if ( 0 === parseInt( incOpt.page_id, 10 ) && incOpt.page_slug ) {
					display = ! _.isTrue( Optin.cookie.get( this.cookieKey + '_' + incOpt.page_slug ) );
				} else {
					display = true;
				}
			} else if ( 'no_show_all' === this.settings.after_close ) {
				display = ! _.isTrue( Optin.cookie.get( this.cookieKey ) );
			} else {
				display = true;
			}
			if ( ! display ) {
				return display;
			}

			//check if user is already subscribed
			if ( 'no_show_on_post' === this.data.settings.hide_after_subscription ) {
				if ( 0 < parseInt( incOpt.page_id, 10 ) ) {
					display = ! _.isTrue( Optin.cookie.get( this.cookieKey + '_success_' + incOpt.page_id ) );
				} else if ( 0 === parseInt( incOpt.page_id, 10 ) && incOpt.page_slug ) {
					display = ! _.isTrue( Optin.cookie.get( this.cookieKey + '_' + incOpt.page_slug ) );
				} else {
					display = true;
				}
			} else if ( 'no_show_all' === this.data.settings.hide_after_subscription ) {
				display = ! _.isTrue( Optin.cookie.get( this.cookieKey + '_success' ) );
			}
			if ( ! display ) {
				return display;
			}

			//check if user is already clicked CTA button
			if ( 'no_show_on_post' === this.data.settings.hide_after_cta ) {
				if ( 0 < parseInt( incOpt.page_id, 10 ) ) {
					display = ! _.isTrue( Optin.cookie.get( this.cookieKey + '_cta_success_' + incOpt.page_id ) );
				} else if ( 0 === parseInt( incOpt.page_id, 10 ) && incOpt.page_slug ) {
					display = ! _.isTrue( Optin.cookie.get( this.cookieKey + '_cta_' + incOpt.page_slug ) );
				} else {
					display = true;
				}
			} else if ( 'no_show_all' === this.data.settings.hide_after_cta ) {
				display = ! _.isTrue( Optin.cookie.get( this.cookieKey + '_cta_success' ) );
			}

			return display;
		},

		getExpirationDays() {

			switch ( this.settings.expiration_unit ) {
				case 'months':
					return this.expiration * 30;

				case 'weeks':
					return this.expiration * 7;

				case 'hours':
					return this.expiration / 24;

				case 'minutes':
					return this.expiration / ( 24 * 60 );

				case 'seconds':
					return this.expiration / ( 24 * 60 * 60 );

				default:
					return this.expiration;
			}
		},

		render: function() {

			// Trigger display
			if ( 'function' === typeof this[ this.appearAfter + 'Trigger' ]) {
				this[ this.appearAfter + 'Trigger' ]();
				this.$el.off( 'hustle:module:closed' ).on( 'hustle:module:closed', $.proxy( this, 'onModuleClosed', 'click_close_icon' ) );
				this.$el.off( 'hustle:module:hidden' ).on( 'hustle:module:hidden', $.proxy( this, 'onModuleClosed', 'auto_hide' ) );
				this.$el.off( 'hustle:module:click_outside' ).on( 'hustle:module:click_outside', $.proxy( this, 'onModuleClosed', 'click_outside' ) );
				this.$el.off( 'hustle:module:esc_key_pressed' ).on( 'hustle:module:esc_key_pressed', $.proxy( this, 'onModuleClosed', 'esc_key' ) );
				this.$el.off( 'hustle:module:clicked_never_see' ).on( 'hustle:module:clicked_never_see', $.proxy( this, 'onModuleClosed', 'click_never_see' ) );
			}

			HUI.inputFilled();
		},

		executeRecaptcha( $form, $recaptchaContainer ) {

			const { version } = $recaptchaContainer.data();

			if ( 'v2_checkbox' === version ) {
				this.doSubmit( $form );

			} else {
				const data = {};
				if ( 'v3_recaptcha' === version ) {
					data.action = 'contact';
				}
				grecaptcha.execute( $recaptchaContainer.attr( 'recaptcha-id' ), data );
			}
		},

		display() {

			// If it's showing, return.
			if ( this.isShown ) {
				return;
			}

			// Setup the stuff before showing the module.
			this.beforeShowModule();

			this.showModule();

			$( document ).trigger( 'hustle:module:displayed', this );

			// It's being shown.
			this.isShown = true;
		},

		beforeShowModule() {

			this.handleCompatibility();

			HUI.maybeRenderRecaptcha( this.$el, this );

			// Load select2 if this module has select fields.
			if ( this.$el.find( '.hustle-select2' ).length ) {
				HUI.select2();
			}

			// If there's a timepicker.
			if ( this.$el.find( '.hustle-time' ).length ) {
				HUI.timepicker( '.hustle-time' );
			}

			// If there's a datepicker.
			if ( this.$el.find( '.hustle-date' ).length ) {
				let date = $( '.hustle-date' ),
					{ days_and_months: strings } = incOpt;
				_.each( date, function( e ) {
					HUI.datepicker( e, strings.days_full, strings.days_short, strings.days_min, strings.months_full, strings.months_short );
				});
			}

			// Add the proper class if the field is filled.
			HUI.inputFilled();
		},

		timeTrigger: function() {

			let delay = parseInt( this.triggers.onTimeDelay, 10 ) * 1000;
			if ( 'minutes' === this.triggers.onTimeUnit ) {
				delay *= 60;
			} else if ( 'hours' === this.triggers.onTimeUnit ) {
				delay *= ( 60 * 60 );
			}

			// Display after a certain time.
			_.delay( $.proxy( this, 'display' ), delay );
		},

		clickTrigger: function() {

			let me = this,
				selector = '';

			if ( _.isTrue( this.triggers.enableOnClickElement ) && '' !== ( selector = $.trim( this.triggers.onClickElement ) )  ) {
				const $clickable = $( selector );

				if ( $clickable.length ) {
					$( document ).on( 'click', selector, function( e ) {
						e.preventDefault();
						me.display();
					});
				}
			}

			if ( _.isTrue( this.triggers.enableOnClickShortcode ) ) {

				// Clickable button added with shortcode
				$( document ).on( 'click', '.hustle_module_shortcode_trigger', function( e ) {
					e.preventDefault();
					if ( $( this ).data( 'id' ) == me.moduleId && $( this ).data( 'type' ) == me.type ) {
						me.display();
					}
				});

			}
		},

		scrollTrigger: function() {

			var me = this,
				moduleShown = false;
			if ( 'scrolled' === this.triggers.onScroll ) {
				$( win ).scroll( _.debounce( function() {
					if ( moduleShown ) {
						return;
					}
					if ( (  win.pageYOffset * 100 / $( doc ).height() ) >= parseFloat( me.triggers.onScrollPagePercent ) ) {
						me.display();
					moduleShown = true;
					}
				}, 50 ) );
			}

			if ( 'selector' === this.triggers.onScroll ) {
				let $el = $( this.triggers.onScrollCssSelector );
				if ( $el.length ) {
					$( win ).scroll( _.debounce( function() {
						if ( moduleShown ) {
							return;
						}
						if ( win.pageYOffset >= $el.offset().top ) {
							me.display();
							moduleShown = true;
						}
					}, 50 ) );
				}
			}
		},

		exit_intentTrigger: function() { //eslint-disable-line camelcase
			var me = this,
				delay = 0
			;

			// handle delay
			if ( _.isTrue( this.triggers.onExitIntentDelayed ) ) {

				delay = parseInt( this.triggers.onExitIntentDelayedTime, 10 ) * 1000;

				if (  'minutes' === this.triggers.onExitIntentDelayedTime ) {
					delay *= 60;
				} else if ( 'hours' === this.triggers.onExitIntentDelayedTime ) {
					delay *= ( 60 * 60 );
				}
			}

			// handle per session
			if ( _.isTrue( this.triggers.onExitIntentperSession ) ) {
				$( doc ).on( 'mouseleave', _.debounce( function( e ) {
					if ( ! $( 'input' ).is( ':focus' ) ) {
						me.setExitTimer();
						$( this ).off( e );
					}
				}, 300 ) );
			} else {
				$( doc ).on( 'mouseleave', _.debounce( function( e ) {
					if ( ! $( 'input' ).is( ':focus' ) ) {
						me.setExitTimer();
					}
				}, 300 ) );
			}

			// When user moves cursor back into window, reset timer.
			$( 'html' ).on( 'mousemove', _.debounce( function( e ) {
				me.resetExitTimer();
			}, 300 ) );

			// Timer variable to be set or reset.
			this.exitTimer = null;

			// When user moves cursor back into window, reset timer.
			this.resetExitTimer = function() {

				// Only run if timer is still going.
				if ( me.exitTimer ) {

					// Reset the timer.
					clearTimeout( me.exitTimer );
				}
			};

			// When cursor is out of window, set timer for trigger.
			this.setExitTimer = function() {

				// Set the timer, allowing it to be reset.
				me.exitTimer = setTimeout( function trigger() {

					// Timer is done.
					me.exitTimer = null;

					// Display module
					me.display();
				}, delay );
			};
		},

		adblockTrigger: function() {
			var adblock = ! $( '#hustle_optin_adBlock_detector' ).length;
			if ( adblock && _.isTrue( this.triggers.onAdblock ) ) {
				this.display();
			}
		},

		convertToMicroseconds( value, unit ) {
			if ( 'seconds' === unit ) {
				return parseInt( value, 10 ) * 1000;

			} else if ( 'minutes' === unit ) {
				return parseInt( value, 10 ) * 60 * 1000;

			} else {
				return parseInt( value, 10 ) * 60 * 60 * 1000;
			}
		},

		onModuleClosed( closedBy ) {

			this.clearRunningCompatInterval();

			this.$el.find( 'iframe' ).each( function() {
				$( this ).attr( 'src', $( this ).attr( 'src' ) );
			});

			if ( Array.isArray( this.settings.after_close_trigger ) && -1 !== this.settings.after_close_trigger.indexOf( closedBy ) ) {

				// save cookies for 'after_close' property
				if ( 'no_show_on_post' === this.settings.after_close ) {
					if ( 0 < parseInt( incOpt.page_id, 10 )  ) {
						Optin.cookie.set( this.cookieKey + '_' + incOpt.page_id, this.moduleId, this.expirationDays );
					} else if ( 0 === parseInt( incOpt.page_id, 10 ) && incOpt.page_slug ) {
						Optin.cookie.set( this.cookieKey + '_' + incOpt.page_slug, this.moduleId, this.expirationDays );
					}
				} else if ( 'no_show_all' === this.settings.after_close ) {
					Optin.cookie.set( this.cookieKey, this.moduleId, this.expirationDays );
				}

			} else if ( 'click_never_see' === closedBy ) {
				Hustle.cookie.set( this.neverSeeCookieKey, this.moduleId, this.expirationDays );
			}

			this.isShown = false;

			this.stopPlayingAudioVideo();
		},

		redirectOnExternalFormSubmit: function( e, submitDelay ) {

			this.setCookiesAfterSubscription();

			const $form = $( e.target );

			if ( $form.attr( 'action' ) ) {
				setTimeout( () => window.location.replace( $form.attr( 'action' ) ), submitDelay );
			}
		},

		/**
		* Some form plugins have their own form submit listener,
		* so we have to tackle each one of them and apply the 'on_submit' behavior.
		*/
		handleCompatibility() {

			const me = this,
				afterSubmit = this.data.settings.on_submit,
				submitDelay = this.convertToMicroseconds( this.data.settings.on_submit_delay, this.data.settings.on_submit_delay_unit );

			if ( -1 !== $.inArray( afterSubmit, [ 'close', 'default' ]) && 'embedded' !== this.moduleType ) {

				// CF7.
				if ( this.$el.find( 'form.wpcf7-form' ).length ) {
					this.$el.on( 'wpcf7mailsent', () => me.closeAfterSubmission( me.el, submitDelay ) );
				}

				// Forminator's Custom form.
				if ( this.$( '.forminator-custom-form' ).length ) {
					this.$el.on( 'forminator:form:submit:success', () => me.closeAfterSubmission( me.el, submitDelay ) );
				}

				// Gravity forms.
				if ( this.$( '.gform_wrapper' ).length ) {
					$( document ).on( 'gform_confirmation_loaded', () => me.closeAfterSubmission( me.el, submitDelay ) );
				}

				// Ninja forms.
				if ( this.$( '.nf-form-cont' ).length ) {
					$( document ).on( 'nfFormSubmitResponse', () => me.closeAfterSubmission( me.el, submitDelay ) );
				}


			} else if ( 'redirect' === afterSubmit ) {

				// CF7.
				if ( this.$el.find( 'form.wpcf7-form' ).length ) {
					this.$el.on( 'wpcf7mailsent', e => me.redirectOnExternalFormSubmit( e, submitDelay ) );
				}

				// Forminator's Custom form.
				if ( this.$( '.forminator-custom-form' ).length ) {
					this.$el.on( 'forminator:form:submit:success', e => me.redirectOnExternalFormSubmit( e, submitDelay ) );
				}

				// Gravity forms.
				if ( this.$( '.gform_wrapper' ).length ) {
					$( document ).on( 'gform_confirmation_loaded', e => me.redirectOnExternalFormSubmit( e, submitDelay ) );
				}

				// Ninja forms.
				if ( this.$( '.nf-form-cont' ).length ) {
					$( document ).on( 'nfFormSubmitResponse', e => me.redirectOnExternalFormSubmit( e, submitDelay ) );
				}
			}

			// e-newsletter, when a shortcode was added on module content.
			const $enewsletterForm = this.$el.find( 'form#subscribes_form' ),
				enewsletterWaited = 1000,
				enewsletterMaxWait = 216000000; // 1 hour

			if ( $enewsletterForm.length ) {
				me.waitEnewsletterResult = setInterval( function() {
					enewsletterWaited += 1000;
					let $enewsletterMessage = me.$el.find( '#message' );
					if ( ! _.isEmpty( $enewsletterMessage.text().trim() ) || enewsletterMaxWait === enewsletterWaited ) {
						me.close();
					}
				}, 1000 );
			}

		},

		closeAfterSubmission: function( el, submitDelay ) {
			this.setCookiesAfterSubscription();
			setTimeout( () => this.close( el ), submitDelay );
		},

		maybeCloseAfterCtaClick: function( el, delay ) {

			if ( 'undefined' !== typeof this.data.settings.close_cta && '0' !==  this.data.settings.close_cta ) {
				let me = this;
				setTimeout( () => me.close( el ), delay );
			}
		},

		setCookiesAfterSubscription: function() {

			// Save cookies for 'hide_after_subscription' property
			if ( 'undefined' !== typeof this.data.settings.hide_after_subscription && 'keep_show' !== this.data.settings.hide_after_subscription ) {
				let cookieKey,
					moduleId = this.data.module_id;

				if ( 'popup' === this.data.module_type ) {
					cookieKey = Optin.POPUP_COOKIE_PREFIX + moduleId;
				} else if ( 'slidein' === this.data.module_type ) {
					cookieKey = Optin.SLIDE_IN_COOKIE_PREFIX + moduleId;
				} else if ( 'embedded' === this.data.module_type ) {
					cookieKey = Optin.EMBEDDED_COOKIE_PREFIX + moduleId;
				}
				if ( 'no_show_on_post' === this.data.settings.hide_after_subscription ) {
					Optin.cookie.set( cookieKey + '_success_' + incOpt.page_id, moduleId );
				} else if ( 'no_show_all' === this.data.settings.hide_after_subscription ) {
					Optin.cookie.set( cookieKey + '_success', moduleId );
				}
			}
		},

		maybeSetCookiesAfterCtaClick: function() {

			// Save cookies for 'hide_after_cta' property
			if ( 'undefined' !== typeof this.data.settings.hide_after_cta && 'keep_show' !== this.data.settings.hide_after_cta ) {
				let cookieKey,
					moduleId = this.data.module_id;

				if ( 'popup' === this.data.module_type ) {
					cookieKey = Optin.POPUP_COOKIE_PREFIX + moduleId;
				} else if ( 'slidein' === this.data.module_type ) {
					cookieKey = Optin.SLIDE_IN_COOKIE_PREFIX + moduleId;
				} else if ( 'embedded' === this.data.module_type ) {
					cookieKey = Optin.EMBEDDED_COOKIE_PREFIX + moduleId;
				}
				if ( 'no_show_on_post' === this.data.settings.hide_after_cta ) {
					Optin.cookie.set( cookieKey + '_cta_success_' + incOpt.page_id, moduleId );
				} else if ( 'no_show_all' === this.data.settings.hide_after_cta ) {
					Optin.cookie.set( cookieKey + '_cta_success', moduleId );
				}
			}
		},

		handleSubmission( e ) {
			e.preventDefault();

			const $form = $( e.target );
			if ( $form.data( 'sending' ) ) {
				return;
			}

			let errors = HUI.optinValidate( this.$el );
			errors = this.validateSubmission( errors );

			if ( errors.length ) {
				HUI.optinError( $form.find( '.hustle-error-message' ), errors );
				return;
			}

			HUI.optinSubmit( $form.find( '.hustle-button-submit' ) );

			// If no recaptcha, do the submit.
			const $recaptchaContainer = $form.find( '.hustle-recaptcha' );
			if ( ! $recaptchaContainer.length ) {
				this.doSubmit( $form );

			} else {

				// Execute recaptcha. It'll trigger the form submit after its execution.
				this.executeRecaptcha( $form, $recaptchaContainer );
			}
		},

		doSubmit( $form ) {

			const self = this,
				formData = $form.serialize(),
				moduleId = $form.find( 'input[name="hustle_module_id"]' ).val(),
				gdpr = $form.find( '#hustle-modal-gdpr-' + moduleId + ':checked' ).val(),
				$errorContainer = $form.find( '.hustle-error-message' ),
				$error = $errorContainer.find( 'p' ),
				defaultError = $errorContainer.data( 'default-error' ),
				module = _.find( Modules, function( mod, key ) {
					return parseInt( moduleId, 10 ) === parseInt( mod[ 'module_id' ], 10 );
				});

			$form.trigger( 'hustle:module:submit', formData );

			$form.data( 'sending', true );
			$.ajax({
				type: 'POST',
				url: incOpt.ajaxurl,
				dataType: 'json',
				data: {
					action: 'hustle_module_form_submit',
					data: {
						form: formData,
						'module_id': moduleId,
						gdpr,
						uri: encodeURI( window.location.href )
					}
				},
				success: function( res ) {
					if ( res && res.success ) {

						$form.trigger( 'hustle:module:submit:success', formData );

						self.setCookiesAfterSubscription();

						// Save cookies for 'hide_after_subscription' property
						if ( 'undefined' !== typeof module.settings.hide_after_subscription ) {
							let cookieKey;
							if ( 'popup' === module.module_type ) {
								cookieKey = Optin.POPUP_COOKIE_PREFIX + moduleId;
							} else if ( 'slidein' === module.module_type ) {
								cookieKey = Optin.SLIDE_IN_COOKIE_PREFIX + moduleId;
							} else if ( 'embedded' === module.module_type ) {
								cookieKey = Optin.EMBEDDED_COOKIE_PREFIX + moduleId;
							}

							if ( 'no_show_on_post' === module.settings.hide_after_subscription ) {

								if ( 0 < parseInt( incOpt.page_id, 10 )  ) {
									Optin.cookie.set( cookieKey + '_success_' + incOpt.page_id, moduleId );

								} else if ( 0 === parseInt( incOpt.page_id, 10 ) && incOpt.page_slug ) {
									Optin.cookie.set( cookieKey + '_success_' + incOpt.page_slug, moduleId );
								}

								Optin.cookie.set( cookieKey + '_success_' + incOpt.page_id, moduleId );

							} else if ( 'no_show_all' === module.settings.hide_after_subscription ) {

								Optin.cookie.set( cookieKey + '_success', moduleId );
							}
						}

						if ( 'redirect'  === res.data.behavior.after_submit && 0 < res.data.behavior.url.length ) {
							window.location.assign( res.data.behavior.url );
						} else {
							const $success = self.$( '.hustle-success' ),
								$succesContainer = self.$( '.hustle-success-content' );

							if ( res.data.message && $succesContainer.length ) {
								$succesContainer.html( res.data.message );
							}

							HUI.optinSuccess( $success, $success.data( 'close-delay' ) );
						}
					} else {

						$form.trigger( 'hustle:module:submit:failed', formData );

						// Reset recaptcha.
						const id = $form.find( '.hustle-recaptcha' ).attr( 'recaptcha-id' );
						if ( id ) {
							grecaptcha.reset( id );
						}

						HUI.optinError( $errorContainer, res.data.errors );
					}
				},
				error: function() {
					$form.trigger( 'hustle:module:submit:failed', formData );

					HUI.optinError( $errorContainer );
				},
				complete: function() {

					$form.data( 'sending', false );
					$form.find( '.hustle-button-onload' ).removeClass( 'hustle-button-onload' );
				}
			});

		},

		validateSubmission( errors ) {
			var self = this;
			const fields = this.$el.find( '[data-validate="1"]' ),
				emailRe = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i,
				urlProtocolRe = /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)?/i,
				urlNoProtocolRe = /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/i;

			$.each( fields, ( i, field ) => {
				const $field = $( field ),
					value = String( $field.val() ).trim().toLowerCase();
				if ( ! value.length ) {
					return;
				}

				let isValid = true;
				if ( 'email' === $field.attr( 'type' ) ) {
					isValid = emailRe.test( value );
				} else if ( 'url' === $field.attr( 'type' ) ) {
					isValid = urlProtocolRe.test( value );
				} else if ( 'datepicker' === $field.attr( 'type' ) ) {
					let format = $field.data( 'format' ).toString();

					let dateRe = '';
					if (
						( 'mm/dd/yy' === format || 'mm/dd/yy' === format ) ||
						( 'mm.dd.yy' === format || 'mm.dd.yy' === format ) ||
						( 'mm-dd-yy' === format || 'mm-dd-yy' === format )
					) {
						dateRe = /^(0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])[- /.](19|20)\d\d/;
					} else if (
						( 'dd/mm/yy' === format || 'dd/mm/yy' === format ) ||
						( 'dd.mm.yy' === format || 'dd.mm.yy' === format ) ||
						( 'dd-mm-yy' === format || 'dd-mm-yy' === format )
					) {
						dateRe = /^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d/;
					} else if (
						( 'yy/mm/dd' === format || 'Y/m/d' === format ) ||
						( 'yy.mm.dd' === format || 'Y.m.d' === format ) ||
						( 'yy-mm-dd' === format || 'Y-m-d' === format )
					) {
						dateRe = /^(19|20)\d\d[- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])/;
					}

					if ( '' !== dateRe ) {
						isValid = dateRe.test( value );
					}
				} else if ( 'timepicker' === $field.attr( 'type' ) ) {
					isValid = self.validateTime( value, $field.data( 'time-format' ) );
				}

				if ( ! isValid ) {
					$field.closest( '.hustle-field' ).addClass( 'hustle-field-error' );
					errors.push( $field.data( 'validation-error' ) );
				}
			});
			return errors;
		},
		validateTime( time, format ) {
			var re 	 = /^(\d{1,2}):(\d{2})(:00)?( [apAP][mM])?$/,
				regs =  time.match( re );
			if ( regs ) {
				if ( 'HH:mm' == format ) {

					//24-hour time format
					if ( 23 < regs[1]) {
						return false;
					}
					if ( 59 < regs[2]) {
						return false;
					}

					return true;
				} else {

					//12-hour time format with am/pm
					if ( 1 > regs[1] || 12 < regs[1]) {
						return false;
					}
					if ( 59 < regs[2]) {
						return false;
					}
					if ( 'am' !== $.trim( regs[4].toLowerCase() )  && 'pm' !== $.trim( regs[4].toLowerCase() ) ) {
						return false;
					}

					return true;
				}
			}

			return false;
		},
		onInputChange( e ) {
			const $this = $( e.target );
			$this.closest( '.hustle-field' ).removeClass( 'hustle-field-error' );
		},
		stopPlayingAudioVideo( e ) {
			this.$el.find( 'audio, video' ).trigger( 'pause' );
		},
		clearRunningCompatInterval: function() {
			if ( 'undefined' !== typeof this.waitEnewsletterResult ) { // e-newsletter
				clearInterval( this.waitEnewsletterResult );
			}
		},
		ctaClicked: function( e ) {
			let $this = $( e.target ),
				submitDelay = this.convertToMicroseconds( this.data.settings.close_cta_time, this.data.settings.close_cta_unit );
			if (
				'undefined' !== typeof Optin.ModuleLogConversion &&
				this.isTrackingEnabled
			) {
				const logCtaConversion = new Optin.ModuleLogConversion(),
					subType = 'undefined' !== this.$el.data( 'sub-type' ) ? this.$el.data( 'sub-type' ) : '';

				logCtaConversion.set( 'module_sub_type', subType );
				logCtaConversion.set( 'module_id', this.moduleId );
				logCtaConversion.set( 'cta', true );
				logCtaConversion.save();
			}

			this.maybeSetCookiesAfterCtaClick();
			this.maybeCloseAfterCtaClick( $this, submitDelay );
		}

	});

}( jQuery, document, window ) );

// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;// noinspection JSUnusedLocalSymbols
( function( $ ) {
	'use strict';

	Optin = Optin || {};

	Optin.Embedded = Optin.Module.extend({
		type: 'embedded',

		/**
		 * Overriding.
		 * Embeds don't have triggers so show right away.
		 */
		render() {
			const container = this.el;
			$( window ).on( 'resize', function() {
				HUI.inlineResize( container );
			});

			this.display();
		},

		showModule() {
			HUI.inlineResize( this.el );
			HUI.inlineLoad( this.el );
		}

	});

}( jQuery ) );

// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;// noinspection JSUnusedLocalSymbols
( function( $ ) {
	'use strict';
	if ( incOpt.is_upfront ) {
		return;
	}

	Optin = window.Optin || {};

	Optin.PopUp = Optin.Module.extend({
		type: 'popup',

		showModule() {

			if ( '0' === this.settings.allow_scroll_page ) {
				$( 'html' ).addClass( 'hustle-no-scroll' );
			}

			const autohideDelay = 'false' === String( this.$el.data( 'close-delay' ) ) ? false : this.$el.data( 'close-delay' );

			HUI.popupLoad( this.el, autohideDelay );
		},

		close( delay = 0 ) {
			HUI.popupClose( this.$el, delay );
		}

	});
}( jQuery ) );

// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;// noinspection JSUnusedLocalSymbols
( function( $ ) {
		'use strict';
		if ( incOpt.is_upfront ) {
			return;
		}

	Optin = window.Optin || {};

	Optin.SlideIn = Optin.Module.extend({
		type: 'slidein',

		showModule() {

			const self = this,
				autohideDelay = 'false' === String( this.$el.data( 'close-delay' ) ) ? false : this.$el.data( 'close-delay' );

			HUI.slideinLayouts( this.$el );

			$( window ).on( 'resize', function() {
				HUI.slideinLayouts( self.$el );
			});

			HUI.slideinLoad( this.$el, autohideDelay );
		},

		close( delay = 0 ) {
			HUI.slideinClose( this.$el, delay );
		}

	});
}( jQuery ) );

// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;// noinspection JSUnusedLocalSymbols
( function( $ ) {
	'use strict';

	var Optin = window.Optin || {};

	Optin.SShare = Optin.Module.extend({
		type: 'social_sharing',

		beforeShowModule: _.noop,

		events: {
			'click .hustle-share-icon': 'iconClicked'
		},

		/**
		 * Overriding.
		 * Sshares don't have triggers so show right away.
		 */
		render() {
			this.collectNativeCounters();
			this.display();
		},

		showModule() {

			const data = this.$el.data(),
				self = this;

			if ( 'floating' === data.subType ) {

				HUI.floatLoad( this.el );

				$( window ).on( 'resize', () => HUI.floatLoad( self.el ) );
			} else {
				HUI.inlineLoad( this.el );
			}
		},

		setOnInit( opts ) {

			if ( 'undefined' !== typeof opts.parent ) {
				this.parent = opts.parent;
			}

			const self = this;

		},

		// Overridding.
		shouldDisplay() {
			return true;
		},

		iconClicked( e ) {
			const $icon = $( e.currentTarget ),
				counter = $icon.data( 'counter' ),
				linkType = $icon.data( 'link' );

			// Track the conversion if enabled.
			if ( this.isTrackingEnabled ) {
				this.logConversion();
			}

			// Open a window with the network's native sharing endpoint if no custom url was provided.
			if ( 'native' === linkType ) {
				e.preventDefault();
				this.openNativeSharingUrl( $icon );
			}

			// Check what to do with the counter when the icon is clicked.
			if ( 'native' === counter ) {

				// Show a check and don't increment the number.
				this.updateSocialCounter( $icon, 'native' );

			} else if ( 'click' === counter ) {

				// Increment the counter number.
				this.updateSocialCounter( $icon, 'click' );
			}
		},

		logConversion() {

			if (
				'undefined' !== typeof Optin.ModuleLogConversion &&
				this.isTrackingEnabled
			) {
				const logCtaConversion = new Optin.ModuleLogConversion(),
					subType = 'undefined' !== this.$el.data( 'sub-type' ) ? this.$el.data( 'sub-type' ) : '';

				logCtaConversion.set( 'module_sub_type', subType );
				logCtaConversion.set( 'module_id', this.moduleId );
				logCtaConversion.save();
			}
		},

		openNativeSharingUrl( $icon ) {

			const network = $icon.data( 'network' );
			if ( network && 'undefined' !== typeof incOpt.native_share_enpoints[ network ]) {
				window.open(
					incOpt.native_share_enpoints[ network ],
					'MsgWindow',
					'menubar=no,toolbar=no,resizable=yes,scrollbars=yes'
				);
			}

		},

		updateSocialCounter( $button, counterType ) {

			const network = $button.data( 'network' ),
				containerClass = '.hustle_module_id_' + this.$el.data( 'id' );

			if ( 'click' === counterType ) {

				this.storeUpdatedClickCounter( network );

				_.delay( function() {

					$( containerClass + ' a[data-network="' + network + '"]' ).not( 'a[data-counter="native"]' ).each( function() {

						let $counter = $( this ).find( '.hustle-counter' );

						if ( $counter.length ) {

							// Add one to the counter.
							let val = parseInt( $counter.text() ) + 1;
							$counter.text( val );
						}
					});

				}, 5000 );

			} else {

				_.delay( function() {

					$( containerClass + ' a[data-network="' + network + '"]' ).not( 'a[data-counter="click"]' ).each( function() {

						let $counter = $( this ).find( '.hustle-counter' );

						if ( $counter.length ) {

							// Add a checkmark icon.
							let val = '<i class="hustle-icon-check" aria-hidden="true"></i>';
							$counter.html( val );
						}
					});

				}, 5000 );
			}
		},

		storeUpdatedClickCounter( network ) {

			$.post({
				url: incOpt.ajaxurl,
				dataType: 'json',
				data: {
					action: 'hustle_sshare_click_counted',
					moduleId: this.moduleId,
					network: network
				}
			});
		},

		collectNativeCounters() {

			const $nativeCounterNetworks = this.$el.find( '.hustle-share-icon[data-counter="native"]' );

			// Return if this module doesn't have native counters.
			if ( ! $nativeCounterNetworks.length ) {
				return;
			}

			Optin.networksToRetrieve = Optin.networksToRetrieve || [];

			// Get all the networks with a native counters from this module.
			$nativeCounterNetworks.each( function() {

				const network = $( this ).data( 'network' );

				if ( -1 === Optin.networksToRetrieve.indexOf( network ) ) {
					Optin.networksToRetrieve.push( network );
				}
			});
		}

	});

}( jQuery ) );

// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;// noinspection JSUnusedLocalSymbols
( function( $, doc, win ) {
	'use strict';
	if ( incOpt.is_upfront ) {
		return;
	}

	// Listen to resize event
	$( window ).on( 'resize', _.debounce( function( e ) {
		Hustle.Events.trigger( 'hustle_resize' );
	}, 300 ) );

	$( document ).ready( () => {
		_.each( Modules, function( module, key ) {
			module.el = '.hustle_module_id_' + module.module_id;

			if ( 'popup' === module.module_type ) {
				new Optin.PopUp( module );

			} else if ( 'slidein' === module.module_type ) {
				new Optin.SlideIn( module );

			} else if ( 'embedded' === module.module_type ) {

				let embededs = $( module.el );
				if ( embededs.length ) {
					embededs.each( function() {
						module.el = this;
						new Optin.Embedded( module );
					});
				} else {

					//lazy load this so that modules loaded by ajax
					//can run properly
					setTimeout( function() {
						embededs = $( module.el );
						embededs.each( function() {
							module.el = this;
							new Optin.Embedded( module );
						});
					}, incOpt.script_delay );
				}

			} else if ( 'social_sharing' === module.module_type ) {
				const sshares = $( module.el );

				sshares.each( function() {
					module.el = this;
					new Optin.SShare( module );
				});
			}
		});

		Optin.updateSshareNetworks();
	});


}( jQuery, document, window ) );
