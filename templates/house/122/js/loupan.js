$(function () {
    var subwayNum = 0;
    $("img").scrollLoading();

    $('.lplist').delegate('.codebox', 'hover', function (event) {
        var type = event.type;
        var url = $(this).parent().find('a').attr('href');
        if (type == "mouseenter") {
            $(this).find('.qrcode').css("display", "block");
            $(this).find('#qrcode').qrcode({
                render: window.applicationCache ? "canvas" : "table",
                width: 74,
                height: 74,
                text: huoniao.toUtf8(url)
            });
        } else {
            $(this).find('.qrcode').css("display", "none");
            $(this).find('#qrcode').html('');
        }
    });
    $('.lplist').delegate('.btn_sc', 'click', function (event) {
        var t = $(this),
            type = t.hasClass("btn_ysc") ? "del" : "add",
            id = t.closest('li').attr('data-id');;
        var userid = $.cookie(cookiePre + "login_user");
        if (userid == null || userid == "") {
            huoniao.login();
            return false;
        }
        if (type == "add") {
            t.addClass("btn_ysc").html("<i></i>已收藏");
        } else {
            t.removeClass("btn_ysc").html("<i></i>收藏");
        }
        $.post("/include/ajax.php?service=member&action=collect&module=house&temp=loupan_detail&type=" + type + "&id=" +
            id);
    });


    /**
     * 筛选变量
     */
    //区域、公交/地铁
    $(".t-fi-item li a").bind("click", function () {
        var t = $(this).parent(),
            index = t.index();
        if (!t.hasClass("curr")) {
            t.addClass("curr").siblings("li").removeClass("curr");
            $(".t-fi .sub-fi").hide();
            $(".t-fi .sub-fi:eq(" + index + ")").show();
        } else {
            t.removeClass("curr");
            $(".t-fi .sub-fi:eq(" + index + ")").hide();
        }
        t.addClass('active').siblings().removeClass('active');
        checkFilter();
    });

    // 判断条件
    function checkFilter(){

    	var html = [], curr, index, txt, id;

    	$('.filterlist dl').each(function(g){
    		var box = $(this);
            var title = box.children('dt').text();
	    	// 区域
    		if(g == 0){
          if($('.t-fi-item .curr').index() == 0){
		    		curr = $('.areabox .pos-item .curr');
		    		index = curr.index();
		    		if(index > 0){
		    			txt = curr.text();
		    			id = curr.attr("data-id");
		    			html.push('<a href="javascript:;" title="'+title+'" class="selected-info" data-group="'+g+'" data-level="1" data-type="addrid" data-id="'+id+'"><span>'+txt+'</span><i class="idel"></i></a>');

		    			curr = $('.areabox .pos-sub-item .curr');
		    			index = curr.index();
			    		if(index > 0){
			    			txt = curr.text();
			    			id = curr.attr("data-business");
			    			html.push('<a href="javascript:;" title="'+title+'" class="selected-info" data-group="'+g+'" data-level="2" data-type="addrid" data-id="'+id+'"><span>'+txt+'</span><i class="idel"></i></a>');
			    		}
		    		}
		    	}else if($('.t-fi-item .curr').index() == 1){
		    		curr = $('.subwaybox .pos-item .curr');
		    		index = curr.index();
		    		if(index > 0){
		    			txt = curr.text();
		    			id = curr.attr("data-id");
		    			html.push('<a href="javascript:;" title="'+title+'" class="selected-info" data-group="'+g+'" data-level="1" data-type="subway" data-id="'+id+'"><span>'+txt+'</span><i class="idel"></i></a>');

		    			curr = $('.ditie-sub-point.show');
		    			index = curr.index();
			    		if(index > 0){
			    			txt = curr.text();
			    			id = curr.attr("data-station");
			    			html.push('<a href="javascript:;" title="'+title+'" class="selected-info" data-group="'+g+'" data-level="2" data-type="station" data-id="'+id+'"><span>'+txt+'</span><i class="idel"></i></a>');
			    		}
		    		}
		    	}
	    	// 价格
    		}else if(g == 1){
		    	curr = box.find('.curr');
		    	index = curr.index();
		    	id = curr.attr('data-id');
		    	if(index > -1){
			    	// 自定义价格
			    	if(curr.hasClass('inp_price')){
			    		var min = $(".inp_price .p1").val();
			    		var max = $(".inp_price .p2").val();
			    		if(min != '' || max != ''){
			    			if(min && max && parseInt(min) > parseInt(max)) {
			    				box.find('dd a:eq(0)').addClass('curr');
			    				$(".inp_price").removeClass('active').children('input[type="input"]').val('');
			    			}else{
				    			html.push('<a href="javascript:;" title="'+title+'" class="selected-info" data-group="'+g+'" data-type="price" data-id="'+min+','+max+'"><span>'+min+'-'+max+'</span><i class="idel"></i></a>');
				    		}
			    		}
			    	}else{
			    		if(index > 0){
				    		txt = curr.text();
				    		html.push('<a href="javascript:;" title="'+title+'" class="selected-info" data-group="'+g+'" data-type="price" data-id="'+id+'"><span>'+txt+'</span><i class="idel"></i></a>');
				    	}
			    	}
			    }
		    // 类型
    		}else if(g == 2){
    			curr = box.find('.curr');
		    	index = curr.index();
		    	txt = curr.text();
		    	id = curr.attr('data-id');
		    	if(index > 0){
		    		html.push('<a href="javascript:;" title="'+title+'" class="selected-info" data-group="'+g+'" data-type="type" data-id="'+id+'"><span>'+txt+'</span><i class="idel"></i></a>');
			    }
			// 更多条件
    		}else{
    			box.find('.item').each(function(){
    				var it = $(this);
					var type = it.attr("data-type");
    				curr = it.find('.active');
    				if(curr.length > 0){
	    				txt = curr.text();
    					index = curr.index();
    					if(index > 0){
		    				id = curr.attr("data-id");
                            title = it.children('label').attr('title');
		    				html.push('<a href="javascript:;" title="'+title+'" class="selected-info" data-group="'+g+'" data-type="'+type+'" data-id="'+id+'"><span>'+txt+'</span><i class="idel"></i></a>');
		    			}
    				}
    			})
    		}
    	})

      if(keywords){
        html.push('<a href="javascript:;" title="关键词" class="selected-info" data-group="keywords" data-type="keywords" data-id="'+keywords+'"><span>'+keywords+'</span><i class="idel"></i></a>');
      }

    	if(html.length){
	    	$(".fi-state").show().children("dd").html(html.join(""));
	    }else{
	    	$(".fi-state").hide().children("dd").html("");
	    }
	    atpage = 1;
	    getList();
    }

    // 筛选区域的点击事件 切换位置筛选类型 curr 价格是否为自定义
    $('.filterlist').delegate('a', 'click', function(){
    	var t = $(this), par = t.closest('dl'), con = t.parent(), index = par.index();
    	if(con.hasClass('area') || con.hasClass('subway') || index == 3 || con.hasClass('pos-item')) return;

    	t.addClass('curr').siblings().removeClass('curr');
      if(index == 1){
    		$('.inp_price').removeClass('curr');
    	}
    	checkFilter();
    })

    // 一级区域
    $('.areabox .pos-item a').click(function () {
        var t = $(this),
            i = t.index(),
            item = t.closest('.areabox').find('.pos-sub-item'),
            id = t.attr("data-id");
        if(t.hasClass('disabled')) return;
        t.addClass('curr').siblings().removeClass('curr');
        item.html('<a href="javascript:;" class="all curr">不限</a>');
        if (i == 0) {
            item.hide();
        }else{
	        $.ajax({
	            url: "/include/ajax.php?service=house&action=addr&type=" + id,
	            type: "GET",
	            dataType: "json",
	            success: function (data) {
	                if (data && data.state == 100) {
	                    var list = [],
	                        info = data.info;
	                    list.push('<a href="javascript:;"  data-area="' + id +
	                        '" data-business="0" class="all curr">不限</a>');
	                    for (var i = 0; i < info.length; i++) {
	                        list.push('<a href="javascript:;" data-area="' + id + '" data-business="' + info[i].id + '">' +
	                            info[i].typename + '</a>');
	                    }
	                    item.html(list.join(""));
	                    item.show();
	                }
	            }
	        });
	     }
       checkFilter();
    })
    // 地铁
    $('.subwaybox .pos-item a').click(function () {
        var t = $(this),
            i = t.index(),
            item = t.closest('.subwaybox').find('.pos-sub-item'),
            id = t.attr("data-id");
        t.addClass('curr').siblings().removeClass('curr');
        $(".pos-sub-item .left-direction").html('');
        $(".pos-sub-item .right-direction").html('');
        if (i == 0) {
            item.hide();
        }else{

	        $.ajax({
	            url: "/include/ajax.php?service=siteConfig&action=subwayStation&type=" + id,
	            type: "GET",
	            dataType: "json",
	            success: function (data) {
	                if (data && data.state == 100) {
	                    var leftlist = [],
	                        midlist = [],
	                        rightlist = [],
	                        info = data.info;
	                    leftlist.push('<div class="ditie-sub-point all show"  data-subway="' + id +
	                        '" data-station="0"><div class="first-line"><div class="station-point sub-pos-unit"><div class="red-point"></div></div></div>');
	                    leftlist.push('<div class="station-name">不限</div>');
	                    leftlist.push('</div>');

	                    for (var i = 0; i < info.length; i++) {
	                        if (i < 13) {
	                            leftlist.push('<div class="ditie-sub-point" data-subway="' + id + '" data-station="' + info[
	                                i].id + '">');
	                            leftlist.push('<div class="first-line">');
	                            if (i == 0) {
	                                leftlist.push('<div class="line-info no-left"></div>');
	                            } else if (i == info.length - 1) {
	                                leftlist.push('<div class="line-info no-right"></div>');
	                            } else {
	                                leftlist.push('<div class="line-info"></div>');
	                            }
	                            leftlist.push('<div class="station-point sub-pos-unit"><div class="red-point"></div></div>');
	                            leftlist.push('</div>');
	                            leftlist.push('<div class="station-name">' + info[i].title + '</div>');
	                            leftlist.push('</div>');
	                        } else if (i == 13) {
	                            midlist.push('<div class="ditie-sub-point" data-subway="' + id + '" data-station="' + info[
	                                i].id + '">');
	                            midlist.push(
	                                '<div class="side-bar right"><div class="heng"></div><div class="shu"></div></div>');
	                            midlist.push('<div class="first-line">');
	                            midlist.push('<div class="line-info"></div>');
	                            midlist.push('<div class="station-point sub-pos-unit "><div class="red-point"></div></div>');
	                            midlist.push('</div>');
	                            midlist.push('<div class="station-name">' + info[i].title + '</div>');
	                            midlist.push('</div>');
	                        } else {
	                            rightlist.push('<div  class="ditie-sub-point" data-subway="' + id + '" data-station="' +
	                                info[i].id + '">');
	                            rightlist.push('<div class="first-line">');
	                            if (i == info.length - 1) {
	                                rightlist.push('<div class="line-info no-left"></div>');
	                            } else {
	                                rightlist.push('<div class="line-info"></div>');
	                            }
	                            rightlist.push(
	                                '<div class="station-point sub-pos-unit "><div class="red-point"></div></div>');
	                            rightlist.push('</div>');
	                            rightlist.push('<div class="station-name">' + info[i].title + '</div>');
	                            rightlist.push('</div>');
	                        }

	                    }
	                    $(".pos-sub-item .left-direction").html(leftlist.join(""));
	                    $(".pos-sub-item .right-direction").html(midlist.join("") + rightlist.join(""));
                      item.show();
	                }
	            }
	        });
		    }
        checkFilter();
    });
    // 地铁二级
    $('.subwaybox').delegate('.ditie-sub-point', 'click', function (event) {
        var t = $(this),
            par = t.closest('.direction-line');
        if (par.hasClass('left-direction')) {
            $('.right-direction .ditie-sub-point').removeClass('show')
        } else {
            $('.left-direction .ditie-sub-point').removeClass('show')
        }
        t.addClass("show").siblings().removeClass("show");

        checkFilter();
    });

	// 自定义价格
	$(".inp_price .btn").click(function () {
        var pri_1 = $(".inp_price .p1").val();
        var pri_2 = $(".inp_price .p2").val();
        price_section = pri_1 + ',' + pri_2;
        if (!pri_1 && !pri_2) {
            $(".inp_price .p1").val('');
            $(".inp_price .p2").val('');
            alert('请输入价格');
        } else if (pri_1 && pri_2 && parseInt(pri_1) > parseInt(pri_2)) {
        	alert('价格上限应该大于下限');
        } else {
            $(this).parents("dl").find('a').removeClass('curr');
            $('.inp_price').addClass('curr');
        	checkFilter();
        }
    })

    //更多筛选条件
    $(".filter .more .item").hover(function () {
        $(this).find("ul").stop().slideDown(150);
    }, function () {
        $(this).find("ul").stop().slideUp(150);
    });

    $('.more .item').delegate('li', 'click', function (event) {
        var t = $(this),par = t.closest('.item'),label = par.find('label'),itemtxt = par.find('span'),ptext = label.attr('title'),id = t.attr('data-id'),text = t.find('a').text();
		if (t.index() == 0) {
            itemtxt.removeClass('cur').html(ptext);
        } else {
			itemtxt.addClass('cur').html(text);
        }
        t.addClass('active').siblings().removeClass('active');
		t.closest("ul").hide();

		checkFilter();

    });

    // 单个删除
    $(".fi-state").delegate(".idel", "click", function () {
        var par = $(this).parent(), group = par.attr('data-group'), level = par.attr("data-level");
        if(group == 0 && level == 1){
            par.siblings('[data-group="0"]').remove();
        }
        par.remove();
        if($(".selected-info").length == 0){
        	$(".fi-state").hide();
        }
        clearFilter(group, level);
        getList();
    });

    // 清空条件
    $(".btn_clear").on("click", function () {
        $(".fi-state").hide().children('dd').html('');
        clearFilter();
        $(".fi-state").hide();
        getList();
    });

    function clearFilter(obj, level){
    	var group = obj ? obj : 'all';
      if(group == 'all' || group == 'keywords') keywords = '';
    	$('.filterlist dl').each(function(g){
    		if(group == 'all' || group == g){
	        	var box = $(this);
	        	if(group == 'all' || g != 0 || (g == 0 && level == 1)){
	        		box.find('.curr').removeClass('curr');
	        		box.find('.cur').removeClass('cur');
	       			box.find('.active').removeClass('active');
	        	}
	        	if(g == 0){

	        		// 清除二级
              if($('.t-fi-item .curr').index() == 0){
		        		$('.areabox .pos-sub-item a:eq(0)').addClass('curr').siblings().removeClass('curr');
              }else if($('.t-fi-item .curr').index() == 1){
	        			$('.ditie-sub-point').removeClass('show')
		        		$('.subwaybox .left-direction .ditie-sub-point:eq(0)').addClass('show');
	        		}

	        		if(level == 1 || level == undefined){
	        			$('.pos-sub-item').hide();
	        			$('.sub-fi').hide();
	        			$('.areabox .pos-item a').eq(0).addClass('curr');
	        			$('.subwaybox .pos-item a').eq(0).addClass('curr');
	        		}

	        	}else if(g == 3){
	        		box.find('.item').each(function(){
	        			var it = $(this), txt = it.children('label').attr('title');
	        			it.find('span').text(txt);
	        		})
	        	}else{
	        		box.find('dd a:eq(0)').addClass('curr');
	        		if(g == 1){
		        		$('.inp_price input[type="input"]').val('');
	        		}
	        	}
	        }
        })
    }


    //排序
    $(".m-t li").bind("click", function () {
        var t = $(this),
            i = t.index(),
            id = t.attr('data-id');
        orderby = id;

        if (!t.hasClass("curr")) {
            t.addClass("curr").siblings("li").removeClass("curr");
        }
        checkFilter();
    });

    $(".m-o a").bind("click", function () {
        var t = $(this),
            i = t.index(),
            id = t.attr('data-id');
        if (i == 1) {
            pantime = id;
        } else if (i == 2) {
            price = id;
        }

        if (!t.hasClass("curr")) {
            t.addClass("curr").siblings("a").removeClass("curr");
        } else {
            if (t.hasClass("curr") && t.hasClass("ob")) {
                t.hasClass("up") ? t.removeClass("up") : t.addClass("up");
            }
        }
        checkFilter();

    });

    checkFilter();

    function getList() {

    	$(".lplist ul").html('<li class="empty">正在获取，请稍后</li>');
    	$(".pagination").html('').hide();

    	var data = [];
    	data.push('page='+atpage);
    	data.push('pageSize='+pageSize);
    	$('.fi-state dd a').each(function(){
    		var t = $(this), type = t.attr('data-type'), id = t.attr('data-id');
    		data.push(type+'='+id);
    	})
    	var curr = $('.m-t li.curr'), id = curr.attr("data-id");
    	if(id == "hot" || id == "tuan"){
    		data.push("filter="+id);
    	}else if(id == "video" || id == "qj" || id == "shapan"){
    		data.push(id+"=1");
    	}
    	var orderby = "";
    	curr = $('.m-l a.curr'), id = curr.attr("data-id");
    	if(id == "time"){
    		if(curr.hasClass("up")){
	    		orderby = 4;
	    	}else{
	    		orderby = 3;
	    	}
    	}else if(id == "price"){
    		if(curr.hasClass("up")){
	    		orderby = 1;
	    	}else{
	    		orderby = 2;
	    	}
    	}
    	data.push("orderby="+orderby);

        $.ajax({
            url: masterDomain + "/include/ajax.php?service=house&action=loupanList",
            type: "get",
            data: data.join("&"),
            dataType: "jsonp",
            success: function (data) {
                if (data.state == 100) {
                    var list = data.info.list,
                        html = [],
                        pageInfo = data.info.pageInfo;
                    $(".totalCount b").html(pageInfo.totalCount);
                    totalCount = pageInfo.totalCount;
                    var atpage = Math.ceil(totalCount / pageSize);

                    for (var i = 0; i < list.length; i++) {
                        var d = list[i];

                        html.push('<li class="fn-clear" data-id="' + d.id + '">');
                        html.push('<div class="imgbox fn-left">');
                        var litpic = d.litpic != "" && d.litpic != undefined ? huoniao.changeFileSize(d.litpic, "small") :
                            "/static/images/404.jpg";
                        html.push('<a href="' + d.url + '" target="_blank"><img src="' + litpic + '"onerror="javascript:this.src=\'/static/images/404.jpg\';" alt="">');
                        var is_Video = '';
                        if (d.videocount) {
                            is_Video = '<i class="ivplay"></i>';
                        }
                        var is_Vr = '';
                        if (d.quanjingcount) {
                            is_Vr = '<i class="ivr"></i>';
                        }
                        html.push(is_Video);
                        html.push(is_Vr);
                        html.push('</a>');
                        html.push('<div class="markbox">');
                        //销售状态
                        var salestate = "";
                        if (d.salestate == 0) {
                            salestate = "<span class='m_mark m_ds'>待售</span>";
                        } else if (d.salestate == 1) {
                            salestate = "<span class='m_mark m_zs'>在售</span>";
                        } else if (d.salestate == 2) {
                            salestate = "<span class='m_mark m_wp'>尾盘</span>";
                        } else if (d.salestate == 3) {
                            salestate = "<span class='m_mark m_sk'>售罄</span>";
                        }

                        var buildstate = "";
                        if (d.existing == 1) {
                            buildstate = '<span class="m_mark2 m_xf">现房</span>';
                        } else if (d.existing == 2) {
                            buildstate = '<span class="m_mark2 m_qf">期房</span>';
                        }
                        html.push(salestate);
                        html.push(buildstate);
                        html.push('</div>');
                        html.push('</div>');
                        html.push('<div class="infobox fn-left">');
                        html.push('<div class="lptit fn-clear">');
                        html.push('<a href="' + d.url + '" target="_blank"><h2>' + d.title + '</h2></a>');
                        if (d.price > 0) {
                            html.push('<span class="lpprice"><b>' + d.price + '</b>' + (d.ptype == 1 ? '元/㎡' : '万元/套') +
                                '</span>');
                        } else {
                            html.push('<span class="lpprice"><b>待定</b></span>');
                        }
                        html.push('</div>');
                        html.push('<div class="lpinf fn-clear">');

                        if (d.hxcount) {
                            var hx = [];
                            for (var n = 0; n < d.hx_room.length; n++) {
                                hx.push(d.hx_room[n] + '居');
                            }
                            hx = hx.join('/ ');
                            if (d.hx_area.length > 0) {
                                hx += ' - ' + d.hx_area[0];
                                hx += '~' + (d.hx_area.length == 2 ? d.hx_area[1] : '') + '平米';
                            }
                        } else {
                            var hx = '暂无户型数据';
                        }

                        html.push('<span class="sp_l fn-left">' + hx + '</span>');
                        //html.push('<span class="sp_r fn-right"><i class="ilptel"></i> ' + d.tel.replace('转',
                        //    '<em>转</em>') + ' </span>');
                        html.push('</div>');
                        html.push('<p class="lpinf">[' + d.addr[d.addr.length - 1] + '] ' + d.address + '</p>');
                        html.push('<p class="lpinf">开发商： ' + d.investor + '</p>');
                        if(d.deliverdate>0){
							html.push('<p class="lpinf">开盘时间：' + huoniao.transTimes(d.deliverdate, 2) + '</p>');
                        }else{
                        	html.push('<p class="lpinf">开盘时间：待定</p>');
                        }
                        html.push('<div class="lpbottom fn-clear">');
                        html.push('<div class="lpmark fn-left">');
                        for (var n = 0; n < d.buildtype.length; n++) {
                            html.push('<span>' + d.buildtype[n] + '</span>');
                        }
                        html.push('</div>');
                        html.push('<div class="btn_box fn-right">');
                        if (d.collect) {
                            html.push('<a href="javascript:;" class="btn_sc btn_ysc"><i class="isc"></i> 已收藏</a>');
                        } else {
                            html.push('<a href="javascript:;" class="btn_sc"><i class="isc"></i> 收藏</a>');
                        }
                      	html.push('<a href="javascript:;" data-title="' + d.title + '" class="btn_share" data-url="'+d.url+'" data-pic="'+litpic+'"><i class="ishare"></i> 分享</a>');
                        html.push('</div>');
                        html.push('</div>');
                        html.push('<div class="codebox"><div class="qrcode"><div id="qrcode"></div></div></div>');
                        html.push('</div>');
                        html.push('</li>');
                    }
                    $(".lplist ul").html(html.join(""));
                    showPageInfo();
                } else {
                	$(".totalCount b").html(0);
                    $(".lplist ul").html('<li class="empty">抱歉！ 未找到相关楼盘</li>');
                }
            },
            error: function(){
              $(".totalCount b").html(0);
              $(".lplist ul").html('<div class="empty">网络错误，请刷新重试</div>');
            }
        })
    }

    //打印分页
    function showPageInfo() {
        var info = $(".pagination");
        var nowPageNum = atpage;
        var allPageNum = Math.ceil(totalCount / pageSize);
        var pageArr = [];

        info.html("").hide();

        //输入跳转
        var redirect = document.createElement("div");
        redirect.className = "pagination-gotopage";
        redirect.innerHTML =
            '<label for="">跳转</label><input type="text" class="inp" maxlength="4" /><input type="button" class="btn" value="GO" />';
        info.append(redirect);

        //分页跳转
        info.find(".btn").bind("click", function () {
            var pageNum = info.find(".inp").val();
            if (pageNum != "" && pageNum >= 1 && pageNum <= Number(allPageNum)) {
                atpage = pageNum;
                getList();
            } else {
                info.find(".inp").focus();
            }
        });

        var pages = document.createElement("div");
        pages.className = "pagination-pages";
        info.append(pages);

        //拼接所有分页
        if (allPageNum > 1) {

            //上一页
            if (nowPageNum > 1) {
                var prev = document.createElement("a");
                prev.className = "prev";
                prev.innerHTML = '上一页';
                prev.setAttribute('href','#');
                prev.onclick = function () {
                    atpage = nowPageNum - 1;
                    getList();
                }
            } else {
                var prev = document.createElement("span");
                prev.className = "prev disabled";
                prev.innerHTML = '上一页';
            }
            info.find(".pagination-pages").append(prev);

            //分页列表
            if (allPageNum - 2 < 1) {
                for (var i = 1; i <= allPageNum; i++) {
                    if (nowPageNum == i) {
                        var page = document.createElement("span");
                        page.className = "curr";
                        page.innerHTML = i;
                    } else {
                        var page = document.createElement("a");
                        page.innerHTML = i;
                        page.setAttribute('href','#');
                        page.onclick = function () {
                            atpage = Number($(this).text());
                            getList();
                        }
                    }
                    info.find(".pagination-pages").append(page);
                }
            } else {
                for (var i = 1; i <= 2; i++) {
                    if (nowPageNum == i) {
                        var page = document.createElement("span");
                        page.className = "curr";
                        page.innerHTML = i;
                    } else {
                        var page = document.createElement("a");
                        page.innerHTML = i;
                        page.setAttribute('href','#');
                        page.onclick = function () {
                            atpage = Number($(this).text());
                            getList();
                        }
                    }
                    info.find(".pagination-pages").append(page);
                }
                var addNum = nowPageNum - 4;
                if (addNum > 0) {
                    var em = document.createElement("span");
                    em.className = "interim";
                    em.innerHTML = "...";
                    info.find(".pagination-pages").append(em);
                }
                for (var i = nowPageNum - 1; i <= nowPageNum + 1; i++) {
                    if (i > allPageNum) {
                        break;
                    } else {
                        if (i <= 2) {
                            continue;
                        } else {
                            if (nowPageNum == i) {
                                var page = document.createElement("span");
                                page.className = "curr";
                                page.innerHTML = i;
                            } else {
                                var page = document.createElement("a");
                                page.innerHTML = i;
                                page.setAttribute('href','#');
                                page.onclick = function () {
                                    atpage = Number($(this).text());
                                    getList();
                                }
                            }
                            // info.find(".pagination-pages").append(page);
                        }
                    }
                }
                var addNum = nowPageNum + 2;
                if (addNum < allPageNum - 1) {
                    var em = document.createElement("span");
                    em.className = "interim";
                    em.innerHTML = "...";
                    info.find(".pagination-pages").append(em);
                }
                for (var i = allPageNum - 1; i <= allPageNum; i++) {
                    if (i <= nowPageNum + 1) {
                        continue;
                    } else {
                        var page = document.createElement("a");
                        page.innerHTML = i;
                        page.setAttribute('href','#');
                        page.onclick = function () {
                            atpage = Number($(this).text());
                            getList();
                        }
                        // info.find(".pagination-pages").append(page);
                    }
                }
            }

            //下一页
            if (nowPageNum < allPageNum) {
                var next = document.createElement("a");
                next.className = "next";
                next.innerHTML = '下一页';
                next.setAttribute('href','#');
                next.onclick = function () {
                    atpage = nowPageNum + 1;
                    getList();
                }
            } else {
                var next = document.createElement("span");
                next.className = "next disabled";
                next.innerHTML = '下一页';
            }
            info.find(".pagination-pages").append(next);

            info.show();

        } else {
            info.hide();
        }
    }


});
