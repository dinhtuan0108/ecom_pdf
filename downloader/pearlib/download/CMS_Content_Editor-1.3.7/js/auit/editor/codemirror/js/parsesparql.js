var SparqlParser=Editor.Parser=(function(){function b(h){return new RegExp("^(?:"+h.join("|")+")$","i")}var g=b(["str","lang","langmatches","datatype","bound","sameterm","isiri","isuri","isblank","isliteral","union","a"]);var f=b(["base","prefix","select","distinct","reduced","construct","describe","ask","from","named","where","order","limit","offset","filter","optional","graph","by","asc","desc",]);var d=/[*+\-<>=&|]/;var c=(function(){function h(l,n){var k=l.next();if(k=="$"||k=="?"){l.nextWhileMatches(/[\w\d]/);return"sp-var"}else{if(k=="<"&&!l.matches(/[\s\u00a0=]/)){l.nextWhileMatches(/[^\s\u00a0>]/);if(l.equals(">")){l.next()}return"sp-uri"}else{if(k=='"'||k=="'"){n(i(k));return null}else{if(/[{}\(\),\.;\[\]]/.test(k)){return"sp-punc"}else{if(k=="#"){while(!l.endOfLine()){l.next()}return"sp-comment"}else{if(d.test(k)){l.nextWhileMatches(d);return"sp-operator"}else{if(k==":"){l.nextWhileMatches(/[\w\d\._\-]/);return"sp-prefixed"}else{l.nextWhileMatches(/[_\w\d]/);if(l.equals(":")){l.next();l.nextWhileMatches(/[\w\d_\-]/);return"sp-prefixed"}var m=l.get(),j;if(g.test(m)){j="sp-operator"}else{if(f.test(m)){j="sp-keyword"}else{j="sp-word"}}return{style:j,content:m}}}}}}}}}function i(j){return function(l,n){var m=false;while(!l.endOfLine()){var k=l.next();if(k==j&&!m){n(h);break}m=!m&&k=="\\"}return"sp-literal"}}return function(k,j){return tokenizer(k,j||h)}})();function a(h){return function(i){var k=i&&i.charAt(0);if(/[\]\}]/.test(k)){while(h&&h.type=="pattern"){h=h.prev}}var j=h&&k==matching[h.type];if(!h){return 0}else{if(h.type=="pattern"){return h.col}else{if(h.align){return h.col-(j?h.width:0)}else{return h.indent+(j?0:indentUnit)}}}}}function e(m){var o=c(m);var l=null,i=0,k=0;function h(q,p){l={prev:l,indent:i,col:k,type:q,width:p}}function n(){l=l.prev}var j={next:function(){var p=o.next(),r=p.style,s=p.content,q=p.value.length;if(s=="\n"){p.indentation=a(l);i=k=0;if(l&&l.align==null){l.align=false}}else{if(r=="whitespace"&&k==0){i=q}else{if(r!="sp-comment"&&l&&l.align==null){l.align=true}}}if(s!="\n"){k+=q}if(/[\[\{\(]/.test(s)){h(s,q)}else{if(/[\]\}\)]/.test(s)){while(l&&l.type=="pattern"){n()}if(l&&s==matching[l.type]){n()}}else{if(s=="."&&l&&l.type=="pattern"){n()}else{if((r=="sp-word"||r=="sp-prefixed"||r=="sp-uri"||r=="sp-var"||r=="sp-literal")&&l&&/[\{\[]/.test(l.type)){h("pattern",q)}}}}return p},copy:function(){var q=l,s=i,r=k,p=o.state;return function(t){o=c(t,p);l=q;i=s;k=r;return j}}};return j}return{make:e,electricChars:"}]"}})();