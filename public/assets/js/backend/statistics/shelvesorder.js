define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'statistics/shelvesorder/index',
                    add_url: '',
                    edit_url: '',
                    del_url: '',
                    multi_url: 'statistics/shelvesorder/multi',
                }
            });

            var table = $("#table");

            var date = new Date();
            var date1 = date.toLocaleDateString();
            date.setDate(date.getDate()+1);
            var date2 = date.toLocaleDateString();



            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'id', title: __('Sid'),operate:false},
                        {field: 'name', title: __('S name'),operate:false},
                        {field: 'status', title: __('Status'),operate:false, formatter: Controller.api.formatter.status},
                        {field: 'num', title: __('Num'),sortable:true, operate:false},
                        {field: 'total', title: __('Total'),sortable:true, operate:false},
                        {field: 'discount', title: __('Discount'),sortable:true, operate:false},
                        {field: 'create_time', title: __('Create time'),defaultValue:date1+'|'+date2, operate: 'BETWEEN', type: 'datetime', addclass: 'datetimepicker', data: 'data-date-format="YYYY-MM-DD HH:mm:ss"'},
                    ]
                ],
                search: false,

            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {//渲染的方法
                status: function (value, row, index) {

                    var color = '';
                    var status_text = '';
                    if(row.status == 0){
                        color = 'info';
                        status_text = __('Not reviewed');
                    }
                    if(row.status == 1){
                        color = 'success';
                        status_text = __('Online');
                    }
                    if(row.status == 2){
                        color = 'danger'
                        status_text = __('Downline');
                    }
                    //渲染状态
                    var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i>'+status_text+'</span>';
                    return html;

                }
            }
        }
    };
    return Controller;
});