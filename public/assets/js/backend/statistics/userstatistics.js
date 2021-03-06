define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'echarts', 'echarts-theme'], function ($, undefined, Backend, Table, Form, Echarts, undefined) {

    var Controller = {
        index: function () {

            var form = $("#one");

            Form.api.bindevent($("form[role=form]"), function (data, ret) {

                Controller.api.charts(data);
            });

            $(".btn-success", form).trigger('click');

        },
        api: {
            charts: function (data) {


                var two = $("#two div");
                two.html('');
                var div1="<label class='control-label col-sm-2'>当月新用户总数<h3>"+data.summary.all_num+"</h3></label>";
                two.append(div1);




                var tbody = $("#table2 tbody");
                tbody.html('');
                $.each(data.all_rows,function(index,item){

                    var div2="<tr><td style='text-align: center; vertical-align: middle;'>" + item.time +
                        "</td><td style='text-align: center; vertical-align: middle;'>" + item.num +"</td></tr>";
                    tbody.append(div2);
                });



                var myChart1 = Echarts.init(document.getElementById('echart'), 'walden');

                // 指定图表的配置项和数据
                var option = {
                    title: {
                        text: data.summary.mouth
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data:['新用户总数']
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    toolbox: {
                        feature: {
                            saveAsImage: {}
                        }
                    },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: data.ddd
                    },
                    yAxis: {
                        type: 'value'
                    },
                    series: [
                        {
                            name:'新用户总数',
                            type:'line',
                            label: {
                                normal: {
                                    show: true,
                                    position: 'top'
                                }
                            },
                            data: data.rows.num
                        }
                    ]
                };

                // 使用刚指定的配置项和数据显示图表。
                myChart1.setOption(option);
            }

        }
    };
    return Controller;
});