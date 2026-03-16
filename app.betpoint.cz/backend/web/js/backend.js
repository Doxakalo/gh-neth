/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/js/backend/components/Tabs.js":
/*!*******************************************!*\
  !*** ./src/js/backend/components/Tabs.js ***!
  \*******************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ Tabs; }
/* harmony export */ });
class Tabs {
	constructor(root) {
		this.root = root;
		this.tabButtons = Array.from(root.querySelectorAll('.tab-button'));
		this.tabContents = Array.from(root.querySelectorAll('[data-tab-content]'));

		this.tabButtons.forEach((btn, idx) => {
			btn.addEventListener('click', () => this.activateTab(idx));
		});

		this.activateTab(0);
	}

	activateTab(index) {
		this.tabButtons.forEach((btn, i) => {
			btn.classList.toggle('active', i === index);
			btn.setAttribute('aria-selected', i === index);
			btn.tabIndex = i === index ? 0 : -1;
		});
		this.tabContents.forEach((content, i) => {
			content.classList.toggle('active', i === index);
		});
	}
}


/***/ }),

/***/ "./src/js/backend/controllers/PageController.js":
/*!******************************************************!*\
  !*** ./src/js/backend/controllers/PageController.js ***!
  \******************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ PageController; }
/* harmony export */ });
class PageController {
	
	constructor() {

		// Initialize the application status check
		const checkInterval = window.appConfig.config.appStatusCheckInterval || 60000;
		this.checkAppStatus();
		setInterval(() => {
			this.checkAppStatus();
		}, checkInterval);
	}

	/**
	 * Checks the application status by making an API call to the backend.
	 * If the status is false, it displays a notification bar with the message.
	 * If the status is true, it hides the notification bar.
	 * 
	 * @returns {void}
	 */
	checkAppStatus() {
		const bar = document.getElementById('app-status-bar');
		fetch(window.appConfig.api.appStatus)
			.then(response => response.json())
			.then(data => {
				if (bar) {
					if (data.status === false) {
						bar.classList.add('visible');
						const paragraph = bar.querySelector('p');
						if (paragraph) {
							paragraph.textContent = data.message;
						}
					} else {
						bar.classList.remove('visible');
					}
				}
			});
	}

}


/***/ }),

/***/ "./src/js/backend/controllers/UserUpdateController.js":
/*!************************************************************!*\
  !*** ./src/js/backend/controllers/UserUpdateController.js ***!
  \************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ UserUpdateController; }
/* harmony export */ });
/* harmony import */ var _PageController__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./PageController */ "./src/js/backend/controllers/PageController.js");


class UserUpdateController extends _PageController__WEBPACK_IMPORTED_MODULE_0__["default"] {

	constructor() {
		super();
	}

}


/***/ }),

/***/ "./src/js/backend/controllers/UserViewController.js":
/*!**********************************************************!*\
  !*** ./src/js/backend/controllers/UserViewController.js ***!
  \**********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ UserViewController; }
/* harmony export */ });
/* harmony import */ var _PageController__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./PageController */ "./src/js/backend/controllers/PageController.js");
/* harmony import */ var _components_Tabs__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../components/Tabs */ "./src/js/backend/components/Tabs.js");



class UserViewController extends _PageController__WEBPACK_IMPORTED_MODULE_0__["default"] {

	constructor() {
		super();

		// init tabs
		const tabsEl = document.getElementById('user-bet-transactions-tabs')
		if (tabsEl) {
			new _components_Tabs__WEBPACK_IMPORTED_MODULE_1__["default"](tabsEl);
		}

		// init callback for tab pjax content load
		window.onUserViewPjaxContentLoad = () => {
			window.scrollTo({ top: 0, behavior: 'smooth' });
			this.initToggleButtons();
		};	

		this.initToggleButtons();
	}


	initToggleButtons() {
		const toggleButtons = document.querySelectorAll('.bet-item .toggle-button');
		toggleButtons.forEach(button => {
			button.addEventListener('click', () => {
				button.closest('.bet-item').classList.toggle('open');
				button.classList.toggle('open');
			});
		});
	}

}


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other entry modules.
!function() {
var __webpack_exports__ = {};
/*!***********************************!*\
  !*** ./src/js/backend/backend.js ***!
  \***********************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _controllers_PageController__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./controllers/PageController */ "./src/js/backend/controllers/PageController.js");
/* harmony import */ var _controllers_UserUpdateController__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./controllers/UserUpdateController */ "./src/js/backend/controllers/UserUpdateController.js");
/* harmony import */ var _controllers_UserViewController__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./controllers/UserViewController */ "./src/js/backend/controllers/UserViewController.js");




onDocumentReady(function() {
	const html = document.querySelector('html');
	const controllerAction = html ? html.dataset.controllerAction : null;

	switch (controllerAction) {
		case 'user/update':
			new _controllers_UserUpdateController__WEBPACK_IMPORTED_MODULE_1__["default"]();
			break;

		case 'user/view':
			new _controllers_UserViewController__WEBPACK_IMPORTED_MODULE_2__["default"]();
			break;
		
		default:
			new _controllers_PageController__WEBPACK_IMPORTED_MODULE_0__["default"]();
			break;
	}
});

function onDocumentReady(fn) {
	if (document.readyState === "complete" || document.readyState === "interactive") {
		setTimeout(fn, 1);
	} else {
		document.addEventListener("DOMContentLoaded", fn);
	}
}

}();
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other entry modules.
!function() {
/*!***************************************!*\
  !*** ./src/sass/backend/backend.scss ***!
  \***************************************/
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin

}();
/******/ })()
;
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiL2JhY2tlbmQvd2ViL2pzL2JhY2tlbmQuanMiLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7Ozs7Ozs7QUFBZTtBQUNmO0FBQ0E7QUFDQTtBQUNBOztBQUVBO0FBQ0E7QUFDQSxHQUFHOztBQUVIO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEdBQUc7QUFDSDtBQUNBO0FBQ0EsR0FBRztBQUNIO0FBQ0E7Ozs7Ozs7Ozs7Ozs7OztBQ3ZCZTtBQUNmO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLEdBQUc7QUFDSDs7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsY0FBYztBQUNkO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsT0FBTztBQUNQO0FBQ0E7QUFDQTtBQUNBLElBQUk7QUFDSjs7QUFFQTs7Ozs7Ozs7Ozs7Ozs7OztBQ3RDOEM7O0FBRS9CLG1DQUFtQyx1REFBYzs7QUFFaEU7QUFDQTtBQUNBOztBQUVBOzs7Ozs7Ozs7Ozs7Ozs7OztBQ1I4QztBQUNSOztBQUV2QixpQ0FBaUMsdURBQWM7O0FBRTlEO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsT0FBTyx3REFBSTtBQUNYOztBQUVBO0FBQ0E7QUFDQSxxQkFBcUIsNEJBQTRCO0FBQ2pEO0FBQ0E7O0FBRUE7QUFDQTs7O0FBR0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsSUFBSTtBQUNKLEdBQUc7QUFDSDs7QUFFQTs7Ozs7OztVQ2xDQTtVQUNBOztVQUVBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBOztVQUVBO1VBQ0E7O1VBRUE7VUFDQTtVQUNBOzs7OztXQ3RCQTtXQUNBO1dBQ0E7V0FDQTtXQUNBLHlDQUF5Qyx3Q0FBd0M7V0FDakY7V0FDQTtXQUNBOzs7OztXQ1BBLDhDQUE4Qzs7Ozs7V0NBOUM7V0FDQTtXQUNBO1dBQ0EsdURBQXVELGlCQUFpQjtXQUN4RTtXQUNBLGdEQUFnRCxhQUFhO1dBQzdEOzs7Ozs7Ozs7Ozs7Ozs7QUNOMEQ7QUFDWTtBQUNKOztBQUVsRTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBLE9BQU8seUVBQW9CO0FBQzNCOztBQUVBO0FBQ0EsT0FBTyx1RUFBa0I7QUFDekI7QUFDQTtBQUNBO0FBQ0EsT0FBTyxtRUFBYztBQUNyQjtBQUNBO0FBQ0EsQ0FBQzs7QUFFRDtBQUNBO0FBQ0E7QUFDQSxHQUFHO0FBQ0g7QUFDQTtBQUNBOzs7Ozs7Ozs7QUM3QkEiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9zcG9ydHMtYmV0dGluZy1jb2xsZWdlLy4vc3JjL2pzL2JhY2tlbmQvY29tcG9uZW50cy9UYWJzLmpzIiwid2VicGFjazovL3Nwb3J0cy1iZXR0aW5nLWNvbGxlZ2UvLi9zcmMvanMvYmFja2VuZC9jb250cm9sbGVycy9QYWdlQ29udHJvbGxlci5qcyIsIndlYnBhY2s6Ly9zcG9ydHMtYmV0dGluZy1jb2xsZWdlLy4vc3JjL2pzL2JhY2tlbmQvY29udHJvbGxlcnMvVXNlclVwZGF0ZUNvbnRyb2xsZXIuanMiLCJ3ZWJwYWNrOi8vc3BvcnRzLWJldHRpbmctY29sbGVnZS8uL3NyYy9qcy9iYWNrZW5kL2NvbnRyb2xsZXJzL1VzZXJWaWV3Q29udHJvbGxlci5qcyIsIndlYnBhY2s6Ly9zcG9ydHMtYmV0dGluZy1jb2xsZWdlL3dlYnBhY2svYm9vdHN0cmFwIiwid2VicGFjazovL3Nwb3J0cy1iZXR0aW5nLWNvbGxlZ2Uvd2VicGFjay9ydW50aW1lL2RlZmluZSBwcm9wZXJ0eSBnZXR0ZXJzIiwid2VicGFjazovL3Nwb3J0cy1iZXR0aW5nLWNvbGxlZ2Uvd2VicGFjay9ydW50aW1lL2hhc093blByb3BlcnR5IHNob3J0aGFuZCIsIndlYnBhY2s6Ly9zcG9ydHMtYmV0dGluZy1jb2xsZWdlL3dlYnBhY2svcnVudGltZS9tYWtlIG5hbWVzcGFjZSBvYmplY3QiLCJ3ZWJwYWNrOi8vc3BvcnRzLWJldHRpbmctY29sbGVnZS8uL3NyYy9qcy9iYWNrZW5kL2JhY2tlbmQuanMiLCJ3ZWJwYWNrOi8vc3BvcnRzLWJldHRpbmctY29sbGVnZS8uL3NyYy9zYXNzL2JhY2tlbmQvYmFja2VuZC5zY3NzP2JmNGUiXSwic291cmNlc0NvbnRlbnQiOlsiZXhwb3J0IGRlZmF1bHQgY2xhc3MgVGFicyB7XG5cdGNvbnN0cnVjdG9yKHJvb3QpIHtcblx0XHR0aGlzLnJvb3QgPSByb290O1xuXHRcdHRoaXMudGFiQnV0dG9ucyA9IEFycmF5LmZyb20ocm9vdC5xdWVyeVNlbGVjdG9yQWxsKCcudGFiLWJ1dHRvbicpKTtcblx0XHR0aGlzLnRhYkNvbnRlbnRzID0gQXJyYXkuZnJvbShyb290LnF1ZXJ5U2VsZWN0b3JBbGwoJ1tkYXRhLXRhYi1jb250ZW50XScpKTtcblxuXHRcdHRoaXMudGFiQnV0dG9ucy5mb3JFYWNoKChidG4sIGlkeCkgPT4ge1xuXHRcdFx0YnRuLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgKCkgPT4gdGhpcy5hY3RpdmF0ZVRhYihpZHgpKTtcblx0XHR9KTtcblxuXHRcdHRoaXMuYWN0aXZhdGVUYWIoMCk7XG5cdH1cblxuXHRhY3RpdmF0ZVRhYihpbmRleCkge1xuXHRcdHRoaXMudGFiQnV0dG9ucy5mb3JFYWNoKChidG4sIGkpID0+IHtcblx0XHRcdGJ0bi5jbGFzc0xpc3QudG9nZ2xlKCdhY3RpdmUnLCBpID09PSBpbmRleCk7XG5cdFx0XHRidG4uc2V0QXR0cmlidXRlKCdhcmlhLXNlbGVjdGVkJywgaSA9PT0gaW5kZXgpO1xuXHRcdFx0YnRuLnRhYkluZGV4ID0gaSA9PT0gaW5kZXggPyAwIDogLTE7XG5cdFx0fSk7XG5cdFx0dGhpcy50YWJDb250ZW50cy5mb3JFYWNoKChjb250ZW50LCBpKSA9PiB7XG5cdFx0XHRjb250ZW50LmNsYXNzTGlzdC50b2dnbGUoJ2FjdGl2ZScsIGkgPT09IGluZGV4KTtcblx0XHR9KTtcblx0fVxufVxuIiwiZXhwb3J0IGRlZmF1bHQgY2xhc3MgUGFnZUNvbnRyb2xsZXIge1xuXHRcblx0Y29uc3RydWN0b3IoKSB7XG5cblx0XHQvLyBJbml0aWFsaXplIHRoZSBhcHBsaWNhdGlvbiBzdGF0dXMgY2hlY2tcblx0XHRjb25zdCBjaGVja0ludGVydmFsID0gd2luZG93LmFwcENvbmZpZy5jb25maWcuYXBwU3RhdHVzQ2hlY2tJbnRlcnZhbCB8fCA2MDAwMDtcblx0XHR0aGlzLmNoZWNrQXBwU3RhdHVzKCk7XG5cdFx0c2V0SW50ZXJ2YWwoKCkgPT4ge1xuXHRcdFx0dGhpcy5jaGVja0FwcFN0YXR1cygpO1xuXHRcdH0sIGNoZWNrSW50ZXJ2YWwpO1xuXHR9XG5cblx0LyoqXG5cdCAqIENoZWNrcyB0aGUgYXBwbGljYXRpb24gc3RhdHVzIGJ5IG1ha2luZyBhbiBBUEkgY2FsbCB0byB0aGUgYmFja2VuZC5cblx0ICogSWYgdGhlIHN0YXR1cyBpcyBmYWxzZSwgaXQgZGlzcGxheXMgYSBub3RpZmljYXRpb24gYmFyIHdpdGggdGhlIG1lc3NhZ2UuXG5cdCAqIElmIHRoZSBzdGF0dXMgaXMgdHJ1ZSwgaXQgaGlkZXMgdGhlIG5vdGlmaWNhdGlvbiBiYXIuXG5cdCAqIFxuXHQgKiBAcmV0dXJucyB7dm9pZH1cblx0ICovXG5cdGNoZWNrQXBwU3RhdHVzKCkge1xuXHRcdGNvbnN0IGJhciA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdhcHAtc3RhdHVzLWJhcicpO1xuXHRcdGZldGNoKHdpbmRvdy5hcHBDb25maWcuYXBpLmFwcFN0YXR1cylcblx0XHRcdC50aGVuKHJlc3BvbnNlID0+IHJlc3BvbnNlLmpzb24oKSlcblx0XHRcdC50aGVuKGRhdGEgPT4ge1xuXHRcdFx0XHRpZiAoYmFyKSB7XG5cdFx0XHRcdFx0aWYgKGRhdGEuc3RhdHVzID09PSBmYWxzZSkge1xuXHRcdFx0XHRcdFx0YmFyLmNsYXNzTGlzdC5hZGQoJ3Zpc2libGUnKTtcblx0XHRcdFx0XHRcdGNvbnN0IHBhcmFncmFwaCA9IGJhci5xdWVyeVNlbGVjdG9yKCdwJyk7XG5cdFx0XHRcdFx0XHRpZiAocGFyYWdyYXBoKSB7XG5cdFx0XHRcdFx0XHRcdHBhcmFncmFwaC50ZXh0Q29udGVudCA9IGRhdGEubWVzc2FnZTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0YmFyLmNsYXNzTGlzdC5yZW1vdmUoJ3Zpc2libGUnKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHR9XG5cbn1cbiIsImltcG9ydCBQYWdlQ29udHJvbGxlciBmcm9tICcuL1BhZ2VDb250cm9sbGVyJztcblxuZXhwb3J0IGRlZmF1bHQgY2xhc3MgVXNlclVwZGF0ZUNvbnRyb2xsZXIgZXh0ZW5kcyBQYWdlQ29udHJvbGxlciB7XG5cblx0Y29uc3RydWN0b3IoKSB7XG5cdFx0c3VwZXIoKTtcblx0fVxuXG59XG4iLCJpbXBvcnQgUGFnZUNvbnRyb2xsZXIgZnJvbSAnLi9QYWdlQ29udHJvbGxlcic7XG5pbXBvcnQgVGFicyBmcm9tICcuLi9jb21wb25lbnRzL1RhYnMnO1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBVc2VyVmlld0NvbnRyb2xsZXIgZXh0ZW5kcyBQYWdlQ29udHJvbGxlciB7XG5cblx0Y29uc3RydWN0b3IoKSB7XG5cdFx0c3VwZXIoKTtcblxuXHRcdC8vIGluaXQgdGFic1xuXHRcdGNvbnN0IHRhYnNFbCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCd1c2VyLWJldC10cmFuc2FjdGlvbnMtdGFicycpXG5cdFx0aWYgKHRhYnNFbCkge1xuXHRcdFx0bmV3IFRhYnModGFic0VsKTtcblx0XHR9XG5cblx0XHQvLyBpbml0IGNhbGxiYWNrIGZvciB0YWIgcGpheCBjb250ZW50IGxvYWRcblx0XHR3aW5kb3cub25Vc2VyVmlld1BqYXhDb250ZW50TG9hZCA9ICgpID0+IHtcblx0XHRcdHdpbmRvdy5zY3JvbGxUbyh7IHRvcDogMCwgYmVoYXZpb3I6ICdzbW9vdGgnIH0pO1xuXHRcdFx0dGhpcy5pbml0VG9nZ2xlQnV0dG9ucygpO1xuXHRcdH07XHRcblxuXHRcdHRoaXMuaW5pdFRvZ2dsZUJ1dHRvbnMoKTtcblx0fVxuXG5cblx0aW5pdFRvZ2dsZUJ1dHRvbnMoKSB7XG5cdFx0Y29uc3QgdG9nZ2xlQnV0dG9ucyA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJy5iZXQtaXRlbSAudG9nZ2xlLWJ1dHRvbicpO1xuXHRcdHRvZ2dsZUJ1dHRvbnMuZm9yRWFjaChidXR0b24gPT4ge1xuXHRcdFx0YnV0dG9uLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgKCkgPT4ge1xuXHRcdFx0XHRidXR0b24uY2xvc2VzdCgnLmJldC1pdGVtJykuY2xhc3NMaXN0LnRvZ2dsZSgnb3BlbicpO1xuXHRcdFx0XHRidXR0b24uY2xhc3NMaXN0LnRvZ2dsZSgnb3BlbicpO1xuXHRcdFx0fSk7XG5cdFx0fSk7XG5cdH1cblxufVxuIiwiLy8gVGhlIG1vZHVsZSBjYWNoZVxudmFyIF9fd2VicGFja19tb2R1bGVfY2FjaGVfXyA9IHt9O1xuXG4vLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcblx0dmFyIGNhY2hlZE1vZHVsZSA9IF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF07XG5cdGlmIChjYWNoZWRNb2R1bGUgIT09IHVuZGVmaW5lZCkge1xuXHRcdHJldHVybiBjYWNoZWRNb2R1bGUuZXhwb3J0cztcblx0fVxuXHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuXHR2YXIgbW9kdWxlID0gX193ZWJwYWNrX21vZHVsZV9jYWNoZV9fW21vZHVsZUlkXSA9IHtcblx0XHQvLyBubyBtb2R1bGUuaWQgbmVlZGVkXG5cdFx0Ly8gbm8gbW9kdWxlLmxvYWRlZCBuZWVkZWRcblx0XHRleHBvcnRzOiB7fVxuXHR9O1xuXG5cdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuXHRfX3dlYnBhY2tfbW9kdWxlc19fW21vZHVsZUlkXShtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuXHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuXHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG59XG5cbiIsIi8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb25zIGZvciBoYXJtb255IGV4cG9ydHNcbl9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIGRlZmluaXRpb24pIHtcblx0Zm9yKHZhciBrZXkgaW4gZGVmaW5pdGlvbikge1xuXHRcdGlmKF9fd2VicGFja19yZXF1aXJlX18ubyhkZWZpbml0aW9uLCBrZXkpICYmICFfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZXhwb3J0cywga2V5KSkge1xuXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIGtleSwgeyBlbnVtZXJhYmxlOiB0cnVlLCBnZXQ6IGRlZmluaXRpb25ba2V5XSB9KTtcblx0XHR9XG5cdH1cbn07IiwiX193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqLCBwcm9wKSB7IHJldHVybiBPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqLCBwcm9wKTsgfSIsIi8vIGRlZmluZSBfX2VzTW9kdWxlIG9uIGV4cG9ydHNcbl9fd2VicGFja19yZXF1aXJlX18uciA9IGZ1bmN0aW9uKGV4cG9ydHMpIHtcblx0aWYodHlwZW9mIFN5bWJvbCAhPT0gJ3VuZGVmaW5lZCcgJiYgU3ltYm9sLnRvU3RyaW5nVGFnKSB7XG5cdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFN5bWJvbC50b1N0cmluZ1RhZywgeyB2YWx1ZTogJ01vZHVsZScgfSk7XG5cdH1cblx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsICdfX2VzTW9kdWxlJywgeyB2YWx1ZTogdHJ1ZSB9KTtcbn07IiwiaW1wb3J0IFBhZ2VDb250cm9sbGVyIGZyb20gJy4vY29udHJvbGxlcnMvUGFnZUNvbnRyb2xsZXInO1xuaW1wb3J0IFVzZXJVcGRhdGVDb250cm9sbGVyIGZyb20gJy4vY29udHJvbGxlcnMvVXNlclVwZGF0ZUNvbnRyb2xsZXInO1xuaW1wb3J0IFVzZXJWaWV3Q29udHJvbGxlciBmcm9tICcuL2NvbnRyb2xsZXJzL1VzZXJWaWV3Q29udHJvbGxlcic7XG5cbm9uRG9jdW1lbnRSZWFkeShmdW5jdGlvbigpIHtcblx0Y29uc3QgaHRtbCA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJ2h0bWwnKTtcblx0Y29uc3QgY29udHJvbGxlckFjdGlvbiA9IGh0bWwgPyBodG1sLmRhdGFzZXQuY29udHJvbGxlckFjdGlvbiA6IG51bGw7XG5cblx0c3dpdGNoIChjb250cm9sbGVyQWN0aW9uKSB7XG5cdFx0Y2FzZSAndXNlci91cGRhdGUnOlxuXHRcdFx0bmV3IFVzZXJVcGRhdGVDb250cm9sbGVyKCk7XG5cdFx0XHRicmVhaztcblxuXHRcdGNhc2UgJ3VzZXIvdmlldyc6XG5cdFx0XHRuZXcgVXNlclZpZXdDb250cm9sbGVyKCk7XG5cdFx0XHRicmVhaztcblx0XHRcblx0XHRkZWZhdWx0OlxuXHRcdFx0bmV3IFBhZ2VDb250cm9sbGVyKCk7XG5cdFx0XHRicmVhaztcblx0fVxufSk7XG5cbmZ1bmN0aW9uIG9uRG9jdW1lbnRSZWFkeShmbikge1xuXHRpZiAoZG9jdW1lbnQucmVhZHlTdGF0ZSA9PT0gXCJjb21wbGV0ZVwiIHx8IGRvY3VtZW50LnJlYWR5U3RhdGUgPT09IFwiaW50ZXJhY3RpdmVcIikge1xuXHRcdHNldFRpbWVvdXQoZm4sIDEpO1xuXHR9IGVsc2Uge1xuXHRcdGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoXCJET01Db250ZW50TG9hZGVkXCIsIGZuKTtcblx0fVxufVxuIiwiLy8gZXh0cmFjdGVkIGJ5IG1pbmktY3NzLWV4dHJhY3QtcGx1Z2luXG5leHBvcnQge307Il0sIm5hbWVzIjpbXSwic291cmNlUm9vdCI6IiJ9