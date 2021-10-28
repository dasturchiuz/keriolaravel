function openCenterWin(url,theWidth,theHeight){
    var theTop=(screen.height/2)-(theHeight/2);
    var theLeft=(screen.width/2)-(theWidth/2);
    var features='height='+theHeight+',width='+theWidth+',top='+theTop+',left='+theLeft+",scrollbars=yes,status=no";
        theWin=window.open(url,'',features);
}
