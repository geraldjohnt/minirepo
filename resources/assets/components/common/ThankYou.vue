<template>
    <div class="thanks">
        <header>
            <div class="thank-you col-md-12 custom-timmer">
                <p id="timer"></p>
                <!-- <p>1日23時59分59秒</p> -->
            </div>
        </header>
        <main>
            <div class="custom-container">
                <div class="col-md-12 main-content">
                    <div class="frst-text">
                        <h1>ご利用頂きありがとうございました</h1>
                        <img src="{{logo}}" alt="application logo">
                    </div>
                    <div class="border"></div>				
                    <div class="row">
                        <div class="col-md-6 scnd-text">
                            <p>御社でもOnline Minimumを利用してみませんか？</p>
                            <p>商談、面接、サポート業務、<br><br> 様々な業務をオンラインで効率化できます。<br><br> 無料で7日間お試し頂けます。</p>
                            <p>まずはOnline Minimumの素晴らしさを知ってください。</p>
                            <div class="link-button">
                                <!-- <a href="https://mee2box.com/sign-up" class="btn">無料で7日間利用する</a> !-->
                            </div>
                        </div>
                        <div class="col-md-6 image">
                            <img src="{{banner}}" alt="mee2bos image">
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
<script>
import { APP_URL } from '../../js/config.js';
import helper from '../../js/helpers.js';
export default {
    // name: 'thank-you',
    data() {
        return {
            PIC_URL : APP_URL,
            logo: `${APP_URL}/image/logo.png`,
            banner: `${APP_URL}/image/mee2box.png`
        }
    },
    methods: {
        timeclock(){
            try {
                let datetoday = new Date();
                let countDownDate = new Date("Sep 12, 2018 16:25:00").getTime();
                let DaysofMonth = new Date(datetoday.getFullYear(), datetoday.getMonth()+1, 0).getDate()
                // console.log("Days : "+DaysofMonth)
                let split = String(datetoday).split(' ');
                let daysSplit = parseInt(split[2])
                let limitDays = daysSplit + 2
                let months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
                let added = (DaysofMonth < limitDays) ? ((limitDays - DaysofMonth) == 0) ? limitDays : limitDays - DaysofMonth  : limitDays;
                let monthUp = (DaysofMonth < limitDays) ? ((limitDays - DaysofMonth) == 0) ? split[1] : months[datetoday.getMonth()+1].substr(0, 3)  : split[1];
                // console.log("ADDED o")
                // console.log(added)
                let addDay = ''+monthUp+' '+added+', '+split[3]+' '+split[4]+'';
                let q = String(addDay)

                let countDownDate2 = new Date(q).getTime();
                localStorage.clear()
                let myStorage = window.localStorage;
                if(myStorage.length == 0){
                  // console.log("TRUE")
                  localStorage.setItem('daysTrial', countDownDate2);
                } else {
                  // console.log("FALSE")
                }

                let DaysTrial = localStorage.getItem('daysTrial')
                return DaysTrial;
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Timeclock for the days trial'}
                let functionName = 'ThankYou:timeclock';
                helper.catchError(errorStats, functionName);
            }
        },
        reload(){
            try {
                if (window.location.href.substr(-2) != '?r') {
                  window.location.replace(window.location.href + '?r');
                  window.location.reload()
                }
            } catch(e) {
                let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Reloading the page'}
                let functionName = 'ThankYou:reload';
                helper.catchError(errorStats, functionName);
            }
        },
    },
    created(){
        window.onbeforeunload = null;
        this.reload()
        document.querySelector('.section').style.display = 'none';
    },
    ready() {
        try {
            // console.clear()
            let vm = this
            // console.log(localStorage.getItem('daysTrial'))
            let DaysTrial = vm.timeclock()
            // Update the count down every 1 second
            let Trial = { DaysTrial: '', status: ''}
            let x = setInterval(function() {
                // Get todays date and time
                let now = new Date().getTime();

                // Find the distance between now and the count down date
                // console.log(Trial)
                let distance = (Trial.status) ? Trial.DaysTrial - now : DaysTrial - now;

                // Time calculations for days, hours, minutes and seconds
                let days = Math.floor(distance / (1000 * 60 * 60 * 24));
                let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Output the result in an element with id="demo"
                if(!!document.getElementById("timer")) {
                  document.getElementById("timer").innerHTML = days + "日 " + hours + "時 "
                + minutes + "分 " + seconds + "秒 ";
                }

                // If the count down is over, write some text
                if (distance <= 0) {
                    // clearInterval(x);
                    localStorage.clear()
                    Trial = {
                      DaysTrial : vm.timeclock(),
                      status: true,
                    }
                    // document.getElementById("timer").innerHTML = "EXPIRED";
                }
            }, 1000);
        } catch(e) {
            let errorStats = {'status':415, 'ErrorTryCatchMsg': e, 'ErrorMsgFromPage':'Object doesnt support this action -> Ready the thank you component'}
            let functionName = 'ThankYou:ready';
            helper.catchError(errorStats, functionName);
        }
    }
}
</script>
<style lang="scss" scoped>
//variables
//colors
$clr-white:#fff;
$clr-lightblue:#1097cf;
$clr-darkblue:#046da2;
$clr-blue:#00b0a6;

header {
    .custom-timmer {
        background: #fff;
        position: relative;
        width: 100%;
        height: 60px;
        padding: 23px;
        box-shadow: 0 2px 5px 0 rgba(0,0,0,.16), 0 2px 10px 0 rgba(0,0,0,.12);
        text-align: center;
        z-index: 1;
    }
    .custom-timmer p {
        color: #1097cf;
        font-size: 20px;
        font-weight: 400;
        margin-bottom: 0;
        margin-top: 0;
        text-shadow: 2px 2px 4px #898989;
    }
}
.row {
    margin-right: -15px;
    margin-left: -15px;
}
//Content
.thanks{
	height:100vh;
  font-family: MS Gothic !important;
  overflow-x:hidden;
}
.main-content{
	.frst-text{
		h1{
			color:$clr-lightblue;
			text-shadow:2px 2px 4px #898989;
            line-height: 40px !important;
		}	
  }
  
  .link-button{
      margin-top: 3rem;
  }
	
	.scnd-text{
		//display: inline-table;
		
		p{
			color:$clr-blue;
			text-shadow:2px 2px 4px #898989;
            line-height: 30px;
		}
		.btn{
			background:$clr-darkblue;
			margin:0;
      color: #fff;
      padding: 12px 0;
      width: 100%;
		} 
		.btn:hover{
			color:$clr-white;
			background:#024364;
		}
	}
	.image{
		margin-top:-120px;
	    img{
	    	width:100%;
	    }
	}
}
@media only screen and (max-height:695px){
.thanks{
    overflow-x:hidden;
  }
}
@media (max-device-height:780px){
.thanks{
    // background-color: transparent!important;
    // width: 100% !important;
    overflow-y: scroll !important;
  }
  .thanks::-webkit-scrollbar
  {
      width:2px;
      background-color: #aaa; 
  }
  
  .thanks::-webkit-scrollbar-track-piece
  {
      background-color:transparent;
  }
}
@media (max-height:600px) {
  .main-content .scnd-text p {
    color: #00b0a6;
    text-shadow: 2px 2px 4px #898989;
    line-height: 20px !important;
  } 
  .main-content .frst-text h1 {
    font-size: 50px;
  }
  .main-content .frst-text img {
    width: 40%;
  }
  .main-content .scnd-text p {
    font-size: 21px;
}
  .main-content .image img {
    width: 100%;
    height: 85%;
  }
}
//header
header{
	.custom-timmer{
		background: $clr-white;
	    position: relative;
	    width: 100%;
	  	height: 60px;
    	padding: 23px;
	    box-shadow: 0 2px 5px 0 rgba(0,0,0,.16), 0 2px 10px 0 rgba(0,0,0,.12);
	    text-align: center;
	    z-index:1;
	    p{
	    	color:$clr-lightblue;
	    	font-size: 20px;
	    	font-weight: 400;
	    	text-shadow:2px 2px 4px #898989;
	    	margin-bottom: 0;
	    }
	}
}
.border{
	background: $clr-darkblue;
	width:40%;
	height:4px;
	margin: 20px 0;
	display: inline-block;
}
//Media queries
@media (min-width: 1281px) {
  
	.custom-container{
		margin: 0 100px;
	}
	.main-content{
		.frst-text{
			h1{
				font-size:55px;
			}
			img{
				width:35%;
			}	
		}
		
		.scnd-text{
			p{
				font-size:26px;
			}
			.btn{
				width:400px;
			} 
		}
	}
  
}//end
@media (min-width: 1025px) and (max-width: 1280px) {
  
  .custom-container{
		margin: 0 50px;
	}
	.main-content{
		.frst-text{
			h1{
				font-size:50px;
			}
			img{
				width:30%;
			}		
		}
		.scnd-text{
			p{
				font-size:24px;
			}
			.btn{
				width:100%;
			} 
		}
	}
  
}//end
@media (min-width: 768px) and (max-width: 1024px) {
  
   .custom-container{
		margin: 0 50px;
	}
	.main-content{
		.frst-text{
			h1{
				font-size:40px;
			}
			img{
				width:40%;
			}		
		}
		.scnd-text{
			p{
				font-size:18px;
			}
			.btn{
				width:100%;
			} 
		}
	}
  
}//end
@media (min-width: 768px) and (max-width: 992px) {
    .custom-container{
		margin: 0 50px;
         .main-content{
            .row{
                    display: -webkit-inline-box;
                .image{
                    margin-top:-90px;
                    img{
                        width:100%;
                    }
                }	
            }
        }
	}
}

@media (min-width: 768px) and (max-width: 992px) and (orientation: portrait){
    .custom-container{
      margin: 0 50px;
         .main-content{
            .row{
                    display: -webkit-inline-box;
                .image{
                    margin-top: 70px;
                    img{
                        width:105%;
                    }
                } 
            }
        }
        .main-content{
            .frst-text{
            h1{
              font-size:35px;
            }
            img {
              width: 70%;
            }
          }

          .border{
            width:100%;
          }
          
          .scnd-text{
            p{
              font-size:18px;
            }
            .btn{
              width:100%;
            } 
          }
        } 
  }
}

@media (min-width: 768px) and (max-width: 1024px) and (orientation: landscape) {
  
    .custom-container{
		margin: 0 30px;
	}
	.main-content{
		.frst-text{
			h1{
				font-size:40px;
			}
			img{
				width:40%;
			}
		}
        
		.scnd-text{
			p{
				font-size:18px;
			}
			.btn{
				width:100%;
			} 
		}
	}
  
}//end
@media (min-width: 481px) and (max-width: 767px) {
  	
  	.custom-container{
		margin: 0 25px;
	}
	.main-content{
		.frst-text{
			h1{
				font-size:35px;
			}
			img{
				width:70%;
			}		
		}
		.border{
			width:100%;
		}
		
		.scnd-text{
			p{
				font-size:18px;
			}
			.btn{
				width:100%;
			} 
		}
		.image{
		    display:none !important;
		}	
	}
  
}//end
@media only screen 
and (min-device-width : 375px) 
and (max-device-width : 667px) 
and (orientation : portrait) { /* STYLES GO HERE */
    .thanks{
      // background-color: transparent!important;
      // width: 100% !important;
      overflow-y: scroll !important;
    }
    .thanks::-webkit-scrollbar
    {
        width:2px;
        background-color: #aaa; 
    }
    
    .thanks::-webkit-scrollbar-track-piece
    {
        background-color:transparent;
    }
    .custom-container{
      margin: 0 5px;
    }
    .main-content{
      padding: 18px 0px !important;
		.frst-text{
			h1{
				font-size:18px !important;
			}
			img{
				width:100%;
			}		
		}
		.border{
			width:100%;
      margin: 10px 0 !important;
		}
		.link-button {
          margin-top: 1.5rem !important;
      }
		.scnd-text{
		
			p{
				font-size: 14px;
        line-height: 14px !important;
        font-weight: 700;
			}
			.btn{
				width:100%;
			} 
		}
		.image{
		    display:none !important;
		}	
	}
 } 
 @media only screen 
and (min-device-width : 414px) 
and (max-device-width : 736px) 
and (orientation : portrait) { 
  /* STYLES GO HERE */ 
  .thanks{
    // background-color: transparent!important;
    // width: 100% !important;
    overflow-y: scroll !important;
  }
  .thanks::-webkit-scrollbar
  {
      width:2px;
      background-color: #aaa; 
  }
  
  .thanks::-webkit-scrollbar-track-piece
  {
      background-color:transparent;
  }
  
  .custom-container{
      margin: 0 5px;
    }
    .main-content{
      padding: 18px 0px !important;
		.frst-text{
			h1{
				font-size:19px !important;
			}
			img{
				width:100%;
			}		
		}
		.border{
			width:100%;
      margin: 10px 0 !important;
		}
		.link-button {
          margin-top: 1.5rem !important;
      }
		.scnd-text{
		
			p{
				font-size: 14px;
        line-height: 17px !important;
        font-weight: 700;
			}
			.btn{
				width:100%;
			} 
		}
		.image{
		    display:none !important;
		}	
	}
  
  
}
@media (min-width: 300px) and (max-width: 480px) {
  
    .custom-container{
		margin: 0 5px;
	}
	.main-content{
    padding: 18px 0px;
		.frst-text{
			h1{
				font-size:18px !important;
			}
			img{
				width:100%;
			}		
		}
		.border{
			width:100%;
      margin: 10px 0;
		}
		.link-button {
          margin-top: 1.5rem;
      }
		.scnd-text{
		
			p{
				font-size: 14px;
        line-height: 14px !important;
        font-weight: 700;
			}
			.btn{
				width:100%;
			} 
		}
		.image{
		    display:none !important;
		}	
	}
  
}//end
@media (max-width: 330px) {
  
    .custom-container{
		margin: 0 5px;
	}
	.main-content{
      padding: 20px 0 !important;
      .frst-text{
          h1{
            font-size:15px !important;
          }
          img{
            width:100%;
          }		      
      }
      .border{
        width:100%;
        margin: 10px 0 !important;
      }
     .link-button {
          margin-top: 1.5rem;
      }
      .scnd-text{
      
          p{
            font-size:12px;
            line-height: 14px !important;
          }
          .btn{
            width:100%;
          } 
      }
      .image{
          display:none !important;
      }	
	}
  
}//end
.btn {
  display: inline-block;
  margin-bottom: 0;
  font-weight: normal;
  text-align: center;
  vertical-align: middle;
  touch-action: manipulation;
  cursor: pointer;
  background-image: none;
  border: 1px solid transparent;
  white-space: nowrap;
  padding: 6px 12px;
  font-size: 14px;
  line-height: 1.42857143;
  border-radius: 4px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
//Col-md
.col-1, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-10, .col-11, .col-12, .col,
.col-auto, .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm,
.col-sm-auto, .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12, .col-md,
.col-md-auto, .col-lg-1, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg,
.col-lg-auto, .col-xl-1, .col-xl-2, .col-xl-3, .col-xl-4, .col-xl-5, .col-xl-6, .col-xl-7, .col-xl-8, .col-xl-9, .col-xl-10, .col-xl-11, .col-xl-12, .col-xl,
.col-xl-auto {
  position: relative;
  width: 100%;
  min-height: 1px;
  padding-right: 15px;
  padding-left: 15px;
}
@media (min-width: 992px) {
  .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12 {
    float: left;
  }
  .col-md-12 {
    width: 100%;
  }
  .col-md-11 {
    width: 91.66666667%;
  }
  .col-md-10 {
    width: 83.33333333%;
  }
  .col-md-9 {
    width: 75%;
  }
  .col-md-8 {
    width: 66.66666667%;
  }
  .col-md-7 {
    width: 58.33333333%;
  }
  .col-md-6 {
    width: 50%;
  }
  .col-md-5 {
    width: 41.66666667%;
  }
  .col-md-4 {
    width: 33.33333333%;
  }
  .col-md-3 {
    width: 25%;
  }
  .col-md-2 {
    width: 16.66666667%;
  }
  .col-md-1 {
    width: 8.33333333%;
  }
}
@media (min-width: 768px) {
  .col-md {
    flex-basis: 0;
    flex-grow: 1;
    max-width: 100%;
  }
  .col-md-auto {
    flex: 0 0 auto;
    width: auto;
    max-width: none;
  }
  .col-md-1 {
    flex: 0 0 8.333333%;
    max-width: 8.333333%;
  }
  .col-md-2 {
    flex: 0 0 16.666667%;
    max-width: 16.666667%;
  }
  .col-md-3 {
    flex: 0 0 25%;
    max-width: 25%;
  }
  .col-md-4 {
    flex: 0 0 33.333333%;
    max-width: 33.333333%;
  }
  .col-md-5 {
    flex: 0 0 41.666667%;
    max-width: 41.666667%;
  }
  .col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
  }
  .col-md-7 {
    flex: 0 0 58.333333%;
    max-width: 58.333333%;
  }
  .col-md-8 {
    flex: 0 0 66.666667%;
    max-width: 66.666667%;
  }
  .col-md-9 {
    flex: 0 0 75%;
    max-width: 75%;
  }
  .col-md-10 {
    flex: 0 0 83.333333%;
    max-width: 83.333333%;
  }
  .col-md-11 {
    flex: 0 0 91.666667%;
    max-width: 91.666667%;
  }
  .col-md-12 {
    flex: 0 0 100%;
    max-width: 100%;
  }
}
</style>
