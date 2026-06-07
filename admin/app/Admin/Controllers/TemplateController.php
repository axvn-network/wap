<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Template;
use App\Models\Template as ModelsTemplate;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Widgets\Card;

class TemplateController extends AdminController
{

    protected $type = [1 => 'WEB',2 => 'WAP',3 => 'APP'];

    public function index(Content $content)
    {
        return $content
            ->header('Quản lý Giao diện')
            ->description('')
            ->body(function (Row $row) {
                $row->column(12, function (Column $column) {
                    $column->row('<a class="btn btn-sm btn-light shadow-none" href="/admin/templates/create">Thêm mới</a>');
                });

                $row->column(12, function (Column $column) {
                    $column->row('<br><span>Đang sử dụng</span>');
                });
                $list = ModelsTemplate::where('state',2)->orderBy('sort','desc')->get();
                foreach ($list as $k => $v) {
                    $row->column(6, function (Column $column) use ($v) {
                        // Tiêu đề và nội dung
                        $card = Card::make($v->name.'-'.$this->type[$v->client_type], "<img src='/uploads/$v->pic' width='300px;' height='400px;'>");
    
                        // Thiết lập nút công cụ
                        // $card->tool('<button class="btn btn-sm btn-light shadow-none">Nút</button>');
    
                        // Thiết lập nội dung chân trang
                        $card->footer('Đường dẫn giao diện:'.$v->template_id);
                        $column->row($card);
                    });
                }
                

                $row->column(12, function (Column $column) {
                    $column->row('<br><span>Giao diện khác</span>');
                });
                $list = ModelsTemplate::where('state','<>',2)->orderBy('sort','desc')->get();
                foreach ($list as $k => $v) {
                    $row->column(6, function (Column $column) use ($v) {
                        // Tiêu đề và nội dung
                        $card = Card::make($v->name.'-'.$this->type[$v->client_type], "<img src='/uploads/$v->pic' width='300px;' height='400px;'>");
    
                        // Thiết lập nút công cụ
                        $card->tool("<a href='/admin/setDefaultTemplate/$v->id/$v->client_type' class='btn btn-sm btn-light shadow-none'>Đặt làm mặc định</a>");
    
                        // Thiết lập nội dung chân trang
                        $card->footer('Đường dẫn giao diện:'.$v->template_id);
                        $column->row($card);
                    });
                }
            });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Template(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('pic');
            $grid->column('client_type');
            $grid->column('sort');
            $grid->column('template_id');
            $grid->column('state');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Template(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('pic');
            $show->field('client_type');
            $show->field('sort');
            $show->field('template_id');
            $show->field('state');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Template(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            $form->image('pic')->uniqueName()->required();
            $form->radio('client_type')->options($this->type)->default(1);
            $form->number('sort');
            $form->text('template_id')->required();
            $form->radio('state')->options([1 => 'Có thể dùng', 0 => 'Vô hiệu hóa', 2 => 'Đang sử dụng'])->default(1);

            $form->saving(function (Form $form) {
                if ($form->state == 2) {
                    ModelsTemplate::where('client_type',$form->type)->where('state','2')->update(['state' => 1]);
                }
            });
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }

    public function setDefaultTemplate($id,$type)
    {
        ModelsTemplate::where('client_type',$type)->where('state','2')->update(['state' => 1]);
        $res = ModelsTemplate::where('id',$id)->update(['state' => 2]);
        admin_success('Thiết lập thành công');
        return redirect('/admin/templates');
    }
}
