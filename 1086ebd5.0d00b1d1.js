(window.webpackJsonp=window.webpackJsonp||[]).push([[5],{101:function(e,t,n){"use strict";n.d(t,"a",(function(){return p})),n.d(t,"b",(function(){return m}));var a=n(0),r=n.n(a);function o(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function i(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);t&&(a=a.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,a)}return n}function c(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?i(Object(n),!0).forEach((function(t){o(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):i(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function l(e,t){if(null==e)return{};var n,a,r=function(e,t){if(null==e)return{};var n,a,r={},o=Object.keys(e);for(a=0;a<o.length;a++)n=o[a],t.indexOf(n)>=0||(r[n]=e[n]);return r}(e,t);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);for(a=0;a<o.length;a++)n=o[a],t.indexOf(n)>=0||Object.prototype.propertyIsEnumerable.call(e,n)&&(r[n]=e[n])}return r}var s=r.a.createContext({}),b=function(e){var t=r.a.useContext(s),n=t;return e&&(n="function"==typeof e?e(t):c(c({},t),e)),n},p=function(e){var t=b(e.components);return r.a.createElement(s.Provider,{value:t},e.children)},u={inlineCode:"code",wrapper:function(e){var t=e.children;return r.a.createElement(r.a.Fragment,{},t)}},d=r.a.forwardRef((function(e,t){var n=e.components,a=e.mdxType,o=e.originalType,i=e.parentName,s=l(e,["components","mdxType","originalType","parentName"]),p=b(n),d=a,m=p["".concat(i,".").concat(d)]||p[d]||u[d]||o;return n?r.a.createElement(m,c(c({ref:t},s),{},{components:n})):r.a.createElement(m,c({ref:t},s))}));function m(e,t){var n=arguments,a=t&&t.mdxType;if("string"==typeof e||a){var o=n.length,i=new Array(o);i[0]=d;var c={};for(var l in t)hasOwnProperty.call(t,l)&&(c[l]=t[l]);c.originalType=e,c.mdxType="string"==typeof e?e:a,i[1]=c;for(var s=2;s<o;s++)i[s]=n[s];return r.a.createElement.apply(null,i)}return r.a.createElement.apply(null,n)}d.displayName="MDXCreateElement"},71:function(e,t,n){"use strict";n.r(t),n.d(t,"frontMatter",(function(){return i})),n.d(t,"metadata",(function(){return c})),n.d(t,"toc",(function(){return l})),n.d(t,"default",(function(){return b}));var a=n(3),r=n(7),o=(n(0),n(101)),i={id:"gettingStarted",title:"Getting Started",sidebar_label:"Getting Started",slug:"/"},c={unversionedId:"gettingStarted",id:"gettingStarted",isDocsHomePage:!1,title:"Getting Started",description:"You can write content using GitHub-flavored Markdown syntax.",source:"@site/docs\\gettingStarted.md",slug:"/",permalink:"/",editUrl:"https://github.com/facebook/docusaurus/edit/master/website/docs/gettingStarted.md",version:"current",sidebar_label:"Getting Started",sidebar:"someSidebar",previous:{title:"Style Guide",permalink:"/doc1"},next:{title:"GitHub deployment flow",permalink:"/github"}},l=[{value:"Purpose of this site",id:"purpose-of-this-site",children:[]},{value:"Tools we are using",id:"tools-we-are-using",children:[{value:"Laravel",id:"laravel",children:[]},{value:"Vue.js",id:"vuejs",children:[]},{value:"MySQL database",id:"mysql-database",children:[]},{value:"Heroku",id:"heroku",children:[]},{value:"Mailgun",id:"mailgun",children:[]},{value:"Asana",id:"asana",children:[]}]},{value:"Creating your local project",id:"creating-your-local-project",children:[]}],s={toc:l};function b(e){var t=e.components,n=Object(r.a)(e,["components"]);return Object(o.b)("wrapper",Object(a.a)({},s,n,{components:t,mdxType:"MDXLayout"}),Object(o.b)("p",null,"You can write content using ",Object(o.b)("a",Object(a.a)({parentName:"p"},{href:"https://github.github.com/gfm/"}),"GitHub-flavored Markdown syntax"),"."),Object(o.b)("h2",{id:"purpose-of-this-site"},"Purpose of this site"),Object(o.b)("p",null,"The purpose of this docs site is to document the progress we are making and any issues we resolve. Many times I have fixed something and find it again down the road and it takes me hours to fix again because I didn't document the fix properly."),Object(o.b)("h2",{id:"tools-we-are-using"},"Tools we are using"),Object(o.b)("h3",{id:"laravel"},"Laravel"),Object(o.b)("p",null,"Laravel is a web application framework with expressive, elegant syntax. A web framework provides a structure and starting point for creating your application, allowing you to focus on creating something amazing while we sweat the details."),Object(o.b)("p",null,"More information: ",Object(o.b)("a",Object(a.a)({parentName:"p"},{href:"https://laravel.com/docs/8.x/installation"}),"https://laravel.com/docs/8.x/installation")),Object(o.b)("h3",{id:"vuejs"},"Vue.js"),Object(o.b)("p",null,"Vue (pronounced /vju\u02d0/, like view) is a progressive framework for building user interfaces. Unlike other monolithic frameworks, Vue is designed from the ground up to be incrementally adoptable. The core library is focused on the view layer only, and is easy to pick up and integrate with other libraries or existing projects. On the other hand, Vue is also perfectly capable of powering sophisticated Single-Page Applications when used in combination with modern tooling and supporting libraries."),Object(o.b)("p",null,"More information: ",Object(o.b)("a",Object(a.a)({parentName:"p"},{href:"https://vuejs.org/v2/guide/"}),"https://vuejs.org/v2/guide/")),Object(o.b)("h3",{id:"mysql-database"},"MySQL database"),Object(o.b)("p",null,"We have an instance of ClearDB running on Heroku. Contact Gabe for credentials. Heroku uses PostgreSQL by default, but most likely the client will be using MySQL so it is better to desing the database on it from the start."),Object(o.b)("p",null,"More information: ",Object(o.b)("a",Object(a.a)({parentName:"p"},{href:"https://www.cleardb.com/#features"}),"https://www.cleardb.com/#features")),Object(o.b)("h3",{id:"heroku"},"Heroku"),Object(o.b)("p",null,"Used to deploy project during development, useful to show to client."),Object(o.b)("p",null,"More information: ",Object(o.b)("a",Object(a.a)({parentName:"p"},{href:"https://www.heroku.com/"}),"https://www.heroku.com/")),Object(o.b)("h3",{id:"mailgun"},"Mailgun"),Object(o.b)("p",null,"SMTP service to send out emails."),Object(o.b)("p",null,"More information: ",Object(o.b)("a",Object(a.a)({parentName:"p"},{href:"https://www.mailgun.com/"}),"https://www.mailgun.com/")),Object(o.b)("h3",{id:"asana"},"Asana"),Object(o.b)("p",null,"For project managament"),Object(o.b)("h2",{id:"creating-your-local-project"},"Creating your local project"),Object(o.b)("pre",null,Object(o.b)("code",Object(a.a)({parentName:"pre"},{}),"git clone https://github.com/GaboxFH/tennis-court-system.git\n")),Object(o.b)("p",null,"After git pull (or git clone ","[repourl]"," the repo if you hadn't created a copy yet), run the following commands:"),Object(o.b)("div",{className:"admonition admonition-important alert alert--info"},Object(o.b)("div",Object(a.a)({parentName:"div"},{className:"admonition-heading"}),Object(o.b)("h5",{parentName:"div"},Object(o.b)("span",Object(a.a)({parentName:"h5"},{className:"admonition-icon"}),Object(o.b)("svg",Object(a.a)({parentName:"span"},{xmlns:"http://www.w3.org/2000/svg",width:"14",height:"16",viewBox:"0 0 14 16"}),Object(o.b)("path",Object(a.a)({parentName:"svg"},{fillRule:"evenodd",d:"M7 2.3c3.14 0 5.7 2.56 5.7 5.7s-2.56 5.7-5.7 5.7A5.71 5.71 0 0 1 1.3 8c0-3.14 2.56-5.7 5.7-5.7zM7 1C3.14 1 0 4.14 0 8s3.14 7 7 7 7-3.14 7-7-3.14-7-7-7zm1 3H6v5h2V4zm0 6H6v2h2v-2z"})))),"important")),Object(o.b)("div",Object(a.a)({parentName:"div"},{className:"admonition-content"}),Object(o.b)("p",{parentName:"div"},"You only have to do this the first time you set up the project"))),Object(o.b)("pre",null,Object(o.b)("code",Object(a.a)({parentName:"pre"},{}),"composer install\n")),Object(o.b)("pre",null,Object(o.b)("code",Object(a.a)({parentName:"pre"},{}),"npm install && npm run dev \n")),Object(o.b)("pre",null,Object(o.b)("code",Object(a.a)({parentName:"pre"},{}),"cp .env.example .env\n")),Object(o.b)("pre",null,Object(o.b)("code",Object(a.a)({parentName:"pre"},{}),"php artisan key:generate\n")),Object(o.b)("pre",null,Object(o.b)("code",Object(a.a)({parentName:"pre"},{}),"php artisan config:cache\n")),Object(o.b)("pre",null,Object(o.b)("code",Object(a.a)({parentName:"pre"},{}),"php artisan serve\n")),Object(o.b)("p",null,"Once you have done this the first time, all you have to run is the following:"),Object(o.b)("div",{className:"admonition admonition-important alert alert--info"},Object(o.b)("div",Object(a.a)({parentName:"div"},{className:"admonition-heading"}),Object(o.b)("h5",{parentName:"div"},Object(o.b)("span",Object(a.a)({parentName:"h5"},{className:"admonition-icon"}),Object(o.b)("svg",Object(a.a)({parentName:"span"},{xmlns:"http://www.w3.org/2000/svg",width:"14",height:"16",viewBox:"0 0 14 16"}),Object(o.b)("path",Object(a.a)({parentName:"svg"},{fillRule:"evenodd",d:"M7 2.3c3.14 0 5.7 2.56 5.7 5.7s-2.56 5.7-5.7 5.7A5.71 5.71 0 0 1 1.3 8c0-3.14 2.56-5.7 5.7-5.7zM7 1C3.14 1 0 4.14 0 8s3.14 7 7 7 7-3.14 7-7-3.14-7-7-7zm1 3H6v5h2V4zm0 6H6v2h2v-2z"})))),"important")),Object(o.b)("div",Object(a.a)({parentName:"div"},{className:"admonition-content"}),Object(o.b)("p",{parentName:"div"},"Run these in separate tabs in your terminal"))),Object(o.b)("pre",null,Object(o.b)("code",Object(a.a)({parentName:"pre"},{}),"npm run watch\n")),Object(o.b)("pre",null,Object(o.b)("code",Object(a.a)({parentName:"pre"},{}),"php artisan serve\n")))}b.isMDXComponent=!0}}]);