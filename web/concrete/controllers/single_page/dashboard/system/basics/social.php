<?

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use \Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Sharing\SocialNetwork\Link;
use Concrete\Core\Sharing\SocialNetwork\ServiceList;
use Concrete\Core\Sharing\SocialNetwork\Service;
use Core;

class Social extends DashboardPageController
{

    public function view()
    {
        $this->set('links', Link::getList());
    }

    public function link_updated()
    {
        $this->set('success', t("Link Updated."));
        $this->view();
    }

    public function link_deleted()
    {
        $this->set('success', t("Link Deleted."));
        $this->view();
    }

    public function link_added()
    {
        $this->set('success', t("Social Link Added."));
        $this->view();
    }

    protected function validatePageRequest($token)
    {
        if (!$this->token->validate($token)) {
            $this->error->add($this->token->getErrorMessage());
        }

        $ssHandle = $this->request->request->get('ssHandle');
        if ($ssHandle) {
            $service = Service::getByHandle($ssHandle);
        }
        $sec = Core::make('helper/security');
        $url = $sec->sanitizeURL($this->request->request->get('url'));
        if (!$url) {
            $this->error->add(t('You must specify a valid URL.'));
        }
        if (!is_object($service)) {
            $this->error->add(t('You must choose a service.'));
        }
        return array($ssHandle, $url);
    }

    public function add_link()
    {
        $r = $this->validatePageRequest('add_link');
        if (!$this->error->has()) {
            list($ssHandle, $url) = $r;
            $link = new Link();
            $link->setServiceHandle($ssHandle);
            $link->setURL($url);
            $link->save();
            $this->redirect('/dashboard/system/basics/social', 'link_added');
        }
        $this->add();
    }

    public function delete_link()
    {
        $slID = $this->request->request->get('slID');
        if (Core::make("helper/validation/numbers")->integer($slID)) {
            if ($slID > 0) {
                $link = Link::getByID($slID);
            }
        }

        if (!is_object($link)) {
            $this->error->add(t('Invalid link.'));
        }
        if (!$this->token->validate('delete_link')) {
            $this->error->add($this->token->getErrorMessage());
        }

        if (!$this->error->has()) {
            $link->delete();
            $this->redirect('/dashboard/system/basics/social', 'link_deleted');
        }

        $this->edit($slID);
    }

    public function edit_link($slID = null)
    {
        $r = $this->validatePageRequest('edit_link');
        $this->edit($slID);
        if (!$this->error->has()) {

            list($ssHandle, $url) = $r;
            $link = $this->socialLink;
            $link->setServiceHandle($ssHandle);
            $link->setURL($url);
            $link->save();
            $this->redirect('/dashboard/system/basics/social', 'link_updated');
        }
    }

    public function add()
    {
        $services = array('' => t('Choose a Service'));
        $list = ServiceList::get();
        foreach($list as $service) {
            $services[$service->getHandle()] = $service->getName();
        }
        $this->set('services', $services);
    }

    public function edit($slID = null)
    {
        if (Core::make("helper/validation/numbers")->integer($slID)) {
            if ($slID > 0) {
                $link = Link::getByID($slID);
            }
        }

        if (!is_object($link)) {
            $this->redirect('/dashboard/system/basics/social');
        }
        $this->socialLink = $link;

        $this->set('link', $link);
        $this->add();
    }

}
