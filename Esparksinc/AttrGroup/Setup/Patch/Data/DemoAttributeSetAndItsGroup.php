<?php

/**
 *  Esparks.
 *
 */

namespace Esparksinc\AttrGroup\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Setup\EavSetup;

class DemoAttributeSetAndItsGroup implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param AttributeSetFactory $attributeSetFactory
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        AttributeSetFactory $attributeSetFactory,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        try {
            $categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);
            $attributeSet = $this->attributeSetFactory->create();
            $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
            $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);
            $data = [
                'attribute_set_name' => 'Demo Attribute Set',
                'entity_type_id' => $entityTypeId,
                'sort_order' => 200,
            ];
            $attributeSet->setData($data);
            $attributeSet->validate();
            $attributeSet->save();
            $attributeSet->initFromSkeleton($attributeSetId);
            $attributeSet->save();
            $firstAttributeGroupName = 'Demo Group1';
            $newAttributeSetId = $categorySetup->getAttributeSetId($entityTypeId, 'Demo Attribute Set');
            $categorySetup->addAttributeGroup(
                $entityTypeId,
                $newAttributeSetId,
                $firstAttributeGroupName,
                200 // sort order
            );
            $firstAttributeGroupId = $categorySetup->getAttributeGroupId(
                $entityTypeId,
                $newAttributeSetId,
                $firstAttributeGroupName
            );
            // Assign the attribute set to the attribute group
            $attributeCode = 'eco_collection'; // Replace with your actual attribute code
            $categorySetup->addAttributeToGroup(
                $entityTypeId,
                $newAttributeSetId,
                $firstAttributeGroupId,
                $attributeCode
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}

  

