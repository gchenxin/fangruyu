define(function(require) {
    var e = require("class"),
    p = require("widgets/area/controller"),
    t = "<p>这里将显示文章的内容。以下是文章内容示例：</p><p>自助建站就是通过一套完善、智能的系统，让不会建设网站的人通过一些非常非常简单的操作就能轻松建立自己的网站。自助建站一般是将已经做好的网站（包含非常多的模版及非常智能化的控制系统）传到网络空间上，然后购买自助建站的人只需登陆后台对其进行一些非常简单的设置，就能建立其个性化的网站。</p><p>自助建站就是一套网站系统，专门给对网页代码或者通俗说不懂制作网站的人使用的一套系统，通过这套系统可以很方便的制作简单网站。当然自助建站只是个网络名词，绝大部分的自助建站所示用的系统都不太一样。</p><p>“会打字就能建网站”，一个会简单计算机操作的人只要几分钟就能快速生成一个企业网站，甚至是各类门户网站。这就是域名注册查询自助建站所提出的网站建设理念。使企事业单位能够快速而有效地以“成本节约、简单易用、维护方便”的方式来建设和实施其先进的电子商务系统，使企业能够通过有效的应用互联网技术来提高企业的运作效率、降低成本、拓展业务，从而实现更大的利润和效益。</p>",
    o = e({
        setup: function() {
            var e = this.getWidget(),
            p = e.getElement();
            p.append(t)
        },
        toJSON: function() {
            return {}
        }
    },
    p);
    return o
});